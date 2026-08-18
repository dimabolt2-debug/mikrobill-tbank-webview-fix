using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Globalization;
using System.IO;
using System.Linq;
using System.Reflection;
using System.Security.Cryptography;
using System.Text;
using System.Windows.Forms;

[assembly: AssemblyTitle("MikroBILL T-Bank Mobile Fix Installer")]
[assembly: AssemblyDescription("Installs the MikroBILL T-Bank mobile browser redirect fix")]
[assembly: AssemblyCompany("dimabolt2-debug")]
[assembly: AssemblyProduct("MikroBILL T-Bank Mobile Fix")]
[assembly: AssemblyVersion("1.1.0.0")]
[assembly: AssemblyFileVersion("1.1.0.0")]

namespace MikroBillTBankFixInstaller
{
    internal sealed class TargetFile
    {
        public string ResourceName;
        public string Destination;
        public string FinalHash;
        public HashSet<string> CompatibleHashes;
        public bool MayBeMissing;

        public TargetFile(string resourceName, string destination, string finalHash, bool mayBeMissing, params string[] compatibleHashes)
        {
            ResourceName = resourceName;
            Destination = destination;
            FinalHash = finalHash;
            MayBeMissing = mayBeMissing;
            CompatibleHashes = new HashSet<string>(compatibleHashes, StringComparer.OrdinalIgnoreCase);
            CompatibleHashes.Add(finalHash);
        }
    }

    internal sealed class BackupItem
    {
        public TargetFile Target;
        public string BackupPath;
        public bool Existed;
    }

    internal static class Program
    {
        private const string FunctionsOriginalHash = "9CB22FF90156B8F38D59AD749FB124D0D9AFE1B439062B9CE2CB271375A5C3E4";
        private const string FunctionsFinalHash = "D1D56AD65D2455801B72A54ADD66549C0DBCA2BAB2678CEE53484784E2C46C67";
        private const string TinkoffOriginalHash = "1F2D61C1FD1B725B743EFE5EF9EC7FFF92C9FAE4EF0590A2B6EFDDD2CBB909EE";
        private const string TinkoffFirstPatchHash = "2CA737268D5D576CD29E1A2227F314E8C42B3CEBF7EF9A2E1D981A4A22839E2F";
        private const string TinkoffFinalHash = "E10C2D123671BFB8B95D60330FCC2732E078C348646A620EC1E193EA1E9A63BB";
        private const string CertificateHash = "A7329A28EA27E0CAF4E3E11E9805C269CB416D5B05F9FE874844568CF11066F6";

        private static readonly List<string> LogLines = new List<string>();

        [STAThread]
        private static int Main(string[] args)
        {
            bool silent = args.Any(a => string.Equals(a, "--silent", StringComparison.OrdinalIgnoreCase));
            bool verifyOnly = args.Any(a => string.Equals(a, "--verify-only", StringComparison.OrdinalIgnoreCase));

            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            try
            {
                List<TargetFile> targets = BuildTargets();
                ValidateEmbeddedPayloads(targets);
                string verification = VerifyTargets(targets);

                if (verifyOnly)
                {
                    ShowMessage(silent, verification, "Проверка MikroBILL", MessageBoxIcon.Information);
                    return 0;
                }

                bool alreadyInstalled = targets.All(t => File.Exists(t.Destination) &&
                    string.Equals(GetSha256(t.Destination), t.FinalHash, StringComparison.OrdinalIgnoreCase));
                if (alreadyInstalled)
                {
                    ShowMessage(silent,
                        "Исправление уже установлено. Все файлы и сертификат прошли проверку.",
                        "MikroBILL T-Bank", MessageBoxIcon.Information);
                    return 0;
                }

                if (!silent)
                {
                    DialogResult confirmation = MessageBox.Show(
                        "Будет установлено исправление оплаты Т-Банка для мобильных браузеров.\r\n\r\n" +
                        "Установщик:\r\n" +
                        "• проверит совместимость файлов;\r\n" +
                        "• создаст резервную копию;\r\n" +
                        "• обновит live, bin\\web и UpdateFiles\\web;\r\n" +
                        "• проверит PHP и хеши;\r\n" +
                        "• автоматически откатит изменения при ошибке.\r\n\r\n" +
                        "Продолжить?",
                        "Установка MikroBILL T-Bank Mobile Fix 1.1",
                        MessageBoxButtons.YesNo,
                        MessageBoxIcon.Question,
                        MessageBoxDefaultButton.Button1);
                    if (confirmation != DialogResult.Yes)
                    {
                        return 1;
                    }
                }

                string backupDirectory = Install(targets);
                ShowMessage(silent,
                    "Исправление успешно установлено.\r\n\r\n" +
                    "Проверены PHP-синтаксис и SHA-256 всех файлов.\r\n" +
                    "Перезапуск Apache не требуется.\r\n\r\n" +
                    "Резервная копия:\r\n" + backupDirectory,
                    "Установка завершена", MessageBoxIcon.Information);
                return 0;
            }
            catch (Exception ex)
            {
                string message = "Установка не выполнена.\r\n\r\n" + ex.Message;
                ShowMessage(silent, message, "Ошибка установки", MessageBoxIcon.Error);
                if (silent)
                {
                    Console.Error.WriteLine(message);
                }
                return 2;
            }
        }

        private static List<TargetFile> BuildTargets()
        {
            string apacheRoot = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.ProgramFiles), "Apache");
            string mikroBillRoot = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.CommonApplicationData), "MikroBILL");

            return new List<TargetFile>
            {
                new TargetFile("Payload.FunctionsPhp", Path.Combine(apacheRoot, "htdocs", "template", "functions.php"), FunctionsFinalHash, false, FunctionsOriginalHash),
                new TargetFile("Payload.Tinkoff2Php", Path.Combine(apacheRoot, "htdocs", "tinkoff2.php"), TinkoffFinalHash, false, TinkoffOriginalHash, TinkoffFirstPatchHash),
                new TargetFile("Payload.FunctionsPhp", Path.Combine(mikroBillRoot, "bin", "web", "template", "functions.php"), FunctionsFinalHash, false, FunctionsOriginalHash),
                new TargetFile("Payload.Tinkoff2Php", Path.Combine(mikroBillRoot, "bin", "web", "tinkoff2.php"), TinkoffFinalHash, false, TinkoffOriginalHash, TinkoffFirstPatchHash),
                new TargetFile("Payload.FunctionsPhp", Path.Combine(mikroBillRoot, "UpdateFiles", "web", "template", "functions.php"), FunctionsFinalHash, false, FunctionsOriginalHash),
                new TargetFile("Payload.Tinkoff2Php", Path.Combine(mikroBillRoot, "UpdateFiles", "web", "tinkoff2.php"), TinkoffFinalHash, false, TinkoffOriginalHash, TinkoffFirstPatchHash),
                new TargetFile("Payload.Certificate", Path.Combine(apacheRoot, "cert", "russian-trusted-root-ca.pem"), CertificateHash, true)
            };
        }

        private static void ValidateEmbeddedPayloads(IEnumerable<TargetFile> targets)
        {
            foreach (TargetFile target in targets.GroupBy(t => t.ResourceName).Select(g => g.First()))
            {
                byte[] payload = ReadResource(target.ResourceName);
                string payloadHash = GetSha256(payload);
                if (!string.Equals(payloadHash, target.FinalHash, StringComparison.OrdinalIgnoreCase))
                {
                    throw new InvalidOperationException("Повреждён встроенный файл " + target.ResourceName + ".");
                }
            }
        }

        private static string VerifyTargets(IEnumerable<TargetFile> targets)
        {
            StringBuilder report = new StringBuilder();
            report.AppendLine("Результат проверки:");
            bool incompatible = false;

            foreach (TargetFile target in targets)
            {
                if (!File.Exists(target.Destination))
                {
                    if (target.MayBeMissing)
                    {
                        report.AppendLine("[будет установлен] " + target.Destination);
                        continue;
                    }
                    incompatible = true;
                    report.AppendLine("[отсутствует] " + target.Destination);
                    continue;
                }

                string hash = GetSha256(target.Destination);
                if (string.Equals(hash, target.FinalHash, StringComparison.OrdinalIgnoreCase))
                {
                    report.AppendLine("[установлено] " + target.Destination);
                }
                else if (target.CompatibleHashes.Contains(hash))
                {
                    report.AppendLine("[совместимо] " + target.Destination);
                }
                else
                {
                    incompatible = true;
                    report.AppendLine("[неизвестная версия] " + target.Destination + " (SHA-256 " + hash + ")");
                }
            }

            if (incompatible)
            {
                throw new InvalidOperationException(
                    report + "\r\nУстановка остановлена, чтобы не повредить другую версию MikroBILL.");
            }

            return report.ToString();
        }

        private static string Install(List<TargetFile> targets)
        {
            VerifyTargets(targets);
            string backupDirectory = Path.Combine(
                Environment.GetFolderPath(Environment.SpecialFolder.CommonApplicationData),
                "MikroBILL", "PatchBackups",
                "TBankMobileRedirectFix-" + DateTime.Now.ToString("yyyyMMdd-HHmmss", CultureInfo.InvariantCulture));
            Directory.CreateDirectory(backupDirectory);

            List<BackupItem> backups = new List<BackupItem>();
            LogLines.Clear();
            Log("Installer version 1.1.0.0");
            Log("Backup directory: " + backupDirectory);

            try
            {
                foreach (TargetFile target in targets)
                {
                    bool existed = File.Exists(target.Destination);
                    string backupPath = Path.Combine(backupDirectory, ToBackupRelativePath(target.Destination));
                    BackupItem item = new BackupItem { Target = target, BackupPath = backupPath, Existed = existed };
                    backups.Add(item);

                    if (existed)
                    {
                        Directory.CreateDirectory(Path.GetDirectoryName(backupPath));
                        File.Copy(target.Destination, backupPath, true);
                        Log("Backed up: " + target.Destination);
                    }
                }

                foreach (TargetFile target in targets)
                {
                    byte[] payload = ReadResource(target.ResourceName);
                    string targetDirectory = Path.GetDirectoryName(target.Destination);
                    Directory.CreateDirectory(targetDirectory);
                    string temporaryPath = target.Destination + ".mikrobill-tbank.tmp";
                    try
                    {
                        File.WriteAllBytes(temporaryPath, payload);
                        if (!string.Equals(GetSha256(temporaryPath), target.FinalHash, StringComparison.OrdinalIgnoreCase))
                        {
                            throw new InvalidOperationException("Ошибка проверки временного файла " + target.Destination + ".");
                        }
                        File.Copy(temporaryPath, target.Destination, true);
                    }
                    finally
                    {
                        if (File.Exists(temporaryPath))
                        {
                            File.Delete(temporaryPath);
                        }
                    }

                    if (!string.Equals(GetSha256(target.Destination), target.FinalHash, StringComparison.OrdinalIgnoreCase))
                    {
                        throw new InvalidOperationException("Ошибка проверки установленного файла " + target.Destination + ".");
                    }
                    Log("Installed: " + target.Destination + " SHA-256 " + target.FinalHash);
                }

                ValidatePhpSyntax(targets);
                WriteRestoreInstructions(backupDirectory, backups);
                WriteLog(backupDirectory);
                return backupDirectory;
            }
            catch (Exception installError)
            {
                Log("Install failed: " + installError.Message);
                try
                {
                    Rollback(backups);
                    Log("Rollback completed.");
                }
                catch (Exception rollbackError)
                {
                    Log("Rollback failed: " + rollbackError.Message);
                    WriteLog(backupDirectory);
                    throw new InvalidOperationException(
                        installError.Message + "\r\n\r\nАвтоматический откат также завершился ошибкой: " + rollbackError.Message +
                        "\r\nРезервная копия: " + backupDirectory);
                }
                WriteLog(backupDirectory);
                throw new InvalidOperationException(installError.Message + "\r\nИзменения автоматически отменены.\r\nРезервная копия: " + backupDirectory);
            }
        }

        private static void ValidatePhpSyntax(IEnumerable<TargetFile> targets)
        {
            string php = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.ProgramFiles), "Apache", "php", "php.exe");
            if (!File.Exists(php))
            {
                throw new FileNotFoundException("Не найден PHP для проверки синтаксиса.", php);
            }

            foreach (string phpFile in targets.Where(t => t.Destination.EndsWith(".php", StringComparison.OrdinalIgnoreCase))
                .Select(t => t.Destination).Distinct(StringComparer.OrdinalIgnoreCase))
            {
                ProcessStartInfo startInfo = new ProcessStartInfo
                {
                    FileName = php,
                    Arguments = "-l \"" + phpFile + "\"",
                    UseShellExecute = false,
                    CreateNoWindow = true,
                    RedirectStandardOutput = true,
                    RedirectStandardError = true
                };
                using (Process process = Process.Start(startInfo))
                {
                    string standardOutput = process.StandardOutput.ReadToEnd();
                    string standardError = process.StandardError.ReadToEnd();
                    process.WaitForExit();
                    Log("PHP lint: " + phpFile + " exit=" + process.ExitCode);
                    if (process.ExitCode != 0)
                    {
                        throw new InvalidOperationException("PHP-проверка не пройдена для " + phpFile + ": " + standardOutput + standardError);
                    }
                }
            }
        }

        private static void Rollback(IEnumerable<BackupItem> backups)
        {
            foreach (BackupItem item in backups.Reverse())
            {
                if (item.Existed)
                {
                    Directory.CreateDirectory(Path.GetDirectoryName(item.Target.Destination));
                    File.Copy(item.BackupPath, item.Target.Destination, true);
                }
                else if (File.Exists(item.Target.Destination))
                {
                    File.Delete(item.Target.Destination);
                }
            }
        }

        private static void WriteRestoreInstructions(string backupDirectory, IEnumerable<BackupItem> backups)
        {
            StringBuilder text = new StringBuilder();
            text.AppendLine("MikroBILL T-Bank Mobile Fix 1.1 backup");
            text.AppendLine();
            text.AppendLine("Для ручного отката запустите командную строку от имени администратора и скопируйте файлы обратно:");
            text.AppendLine();
            foreach (BackupItem item in backups.Where(b => b.Existed))
            {
                text.AppendLine(item.BackupPath + " -> " + item.Target.Destination);
            }
            File.WriteAllText(Path.Combine(backupDirectory, "RESTORE.txt"), text.ToString(), Encoding.UTF8);
        }

        private static void WriteLog(string backupDirectory)
        {
            try
            {
                File.WriteAllLines(Path.Combine(backupDirectory, "install.log"), LogLines.ToArray(), Encoding.UTF8);
            }
            catch
            {
            }
        }

        private static void Log(string message)
        {
            LogLines.Add(DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss", CultureInfo.InvariantCulture) + " " + message);
        }

        private static string ToBackupRelativePath(string path)
        {
            string root = Path.GetPathRoot(path);
            string withoutRoot = path.Substring(root.Length).TrimStart(Path.DirectorySeparatorChar, Path.AltDirectorySeparatorChar);
            string drive = root.Replace(":", string.Empty).Trim(Path.DirectorySeparatorChar, Path.AltDirectorySeparatorChar);
            return Path.Combine(drive, withoutRoot);
        }

        private static byte[] ReadResource(string resourceName)
        {
            using (Stream stream = Assembly.GetExecutingAssembly().GetManifestResourceStream(resourceName))
            {
                if (stream == null)
                {
                    throw new InvalidOperationException("Не найден встроенный файл " + resourceName + ".");
                }
                using (MemoryStream memory = new MemoryStream())
                {
                    stream.CopyTo(memory);
                    return memory.ToArray();
                }
            }
        }

        private static string GetSha256(string filePath)
        {
            using (FileStream stream = File.OpenRead(filePath))
            using (SHA256 sha = SHA256.Create())
            {
                return ToHex(sha.ComputeHash(stream));
            }
        }

        private static string GetSha256(byte[] data)
        {
            using (SHA256 sha = SHA256.Create())
            {
                return ToHex(sha.ComputeHash(data));
            }
        }

        private static string ToHex(byte[] bytes)
        {
            StringBuilder result = new StringBuilder(bytes.Length * 2);
            foreach (byte value in bytes)
            {
                result.Append(value.ToString("X2", CultureInfo.InvariantCulture));
            }
            return result.ToString();
        }

        private static void ShowMessage(bool silent, string message, string title, MessageBoxIcon icon)
        {
            if (!silent)
            {
                MessageBox.Show(message, title, MessageBoxButtons.OK, icon);
            }
        }
    }
}

<?php
namespace App\CPU;
use Carbon\Carbon;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\BusinessSetting;
use App\Models\Currency;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }
    public static function currency_code()
    {
        $currency_code = BusinessSetting::where(['key' => 'currency'])->first()->value;
        return $currency_code;
    }

    public static function currency_symbol()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        return $currency_symbol;
    }
    public static function upload(string $dir, string $format = APPLICATION_IMAGE_FORMAT, array|object|null $image = null) {
        if (!$image) {
            return null;
        }

        set_time_limit(300);

        $dir = rtrim($dir, '/') . '/';
        $sourcePath = $image instanceof UploadedFile
            ? $image->getRealPath()
            : $image;

        $info = @getimagesize($sourcePath);
        if (!$info || empty($info['mime'])) {
            return false;
        }

        $mime = strtolower($info['mime']);
        $supportsAlpha = in_array($mime, ['image/png', 'image/webp'], true);

        // Detect format safely
        $format = match ($mime) {
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => $format,
        };

        if ($format === 'webp' && !function_exists('imagewebp')) {
            $format = $supportsAlpha ? 'png' : 'jpg';
        }

        $imageName = Carbon::now()->format('Y-m-d') . '-' . uniqid() . '.' . $format;

        // Ensure directory exists
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $savePath = storage_path("app/public/{$dir}{$imageName}");

        /**
         * 🚨 IMPORTANT
         * Never process GIF with GD (animation will break)
         */
        if ($mime === 'image/gif') {
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        /**
         * WEBP copy-only if already webp
         */
        if ($mime === 'image/webp' && $format === 'webp') {
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        if ($mime === 'image/webp' && !function_exists('imagecreatefromwebp')) {
            $imageName = Carbon::now()->format('Y-m-d') . '-' . uniqid() . '.webp';
            $savePath = storage_path("app/public/{$dir}{$imageName}");
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        /**
         * Create GD image
         */
        $gdImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png'  => imagecreatefrompng($sourcePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($sourcePath) : false,
            default      => false,
        };

        if (!$gdImage) {
            return false;
        }

        if (!imageistruecolor($gdImage)) {
            imagepalettetotruecolor($gdImage);
        }

        /**
         * Preserve transparency
         */
        if ($supportsAlpha) {
            imagealphablending($gdImage, false);
            imagesavealpha($gdImage, true);
        }

        /**
         * Resize logic
         */
        $maxSize = 2500;
        $width   = imagesx($gdImage);
        $height  = imagesy($gdImage);

        if ($width > $maxSize || $height > $maxSize) {
            $ratio = min($maxSize / $width, $maxSize / $height);
            $newW  = (int)($width * $ratio);
            $newH  = (int)($height * $ratio);

            $temp = imagecreatetruecolor($newW, $newH);

            if ($supportsAlpha) {
                imagealphablending($temp, false);
                imagesavealpha($temp, true);
            }

            imagecopyresampled(
                $temp,
                $gdImage,
                0,
                0,
                0,
                0,
                $newW,
                $newH,
                $width,
                $height
            );

            imagedestroy($gdImage);
            $gdImage = $temp;
        }

        /**
         * Save image
         */
        $saved = match ($format) {
            'jpg', 'jpeg' => imagejpeg($gdImage, $savePath, 85),
            'png'         => imagepng($gdImage, $savePath, -1),
            'webp'        => imagewebp($gdImage, $savePath, 78),
            default       => false,
        };

        imagedestroy($gdImage);

        return $saved ? $imageName : false;
    }

    public static function update(string $dir, $old_image, string $format, $image = null)
    {
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $imageName = Helpers::upload($dir, $format, $image);
        return $imageName;
    }
    public static function delete($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }
        return [
            'success' => 1,
            'message' => translate('Removed successfully')
        ];
    }
    public static function discount_calculate($product, $price)
    {
        if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }
    public static function tax_calculate($product, $price)
    {
        $productDiscountPrice = $price - Helpers::discount_calculate($product, $price);
        $price_tax = ($productDiscountPrice / 100) * $product['tax'];

        return $price_tax;
    }
    public static function get_business_settings($name)
    {
        $config = null;
        $data = BusinessSetting::where(['key' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }
    public static function get_language_name($key)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian - shqip",
            "am" => "Amharic - አማርኛ",
            "ar" => "Arabic - العربية",
            "an" => "Aragonese - aragonés",
            "hy" => "Armenian - հայերեն",
            "ast" => "Asturian - asturianu",
            "az" => "Azerbaijani - azərbaycan dili",
            "eu" => "Basque - euskara",
            "be" => "Belarusian - беларуская",
            "bn" => "Bengali - বাংলা",
            "bs" => "Bosnian - bosanski",
            "br" => "Breton - brezhoneg",
            "bg" => "Bulgarian - български",
            "ca" => "Catalan - català",
            "ckb" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "zh" => "Chinese - 中文",
            "zh-HK" => "Chinese (Hong Kong) - 中文（香港）",
            "zh-CN" => "Chinese (Simplified) - 中文（简体）",
            "zh-TW" => "Chinese (Traditional) - 中文（繁體）",
            "co" => "Corsican",
            "hr" => "Croatian - hrvatski",
            "cs" => "Czech - čeština",
            "da" => "Danish - dansk",
            "nl" => "Dutch - Nederlands",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-CA" => "English (Canada)",
            "en-IN" => "English (India)",
            "en-NZ" => "English (New Zealand)",
            "en-ZA" => "English (South Africa)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (United States)",
            "eo" => "Esperanto - esperanto",
            "et" => "Estonian - eesti",
            "fo" => "Faroese - føroyskt",
            "fil" => "Filipino",
            "fi" => "Finnish - suomi",
            "fr" => "French - français",
            "fr-CA" => "French (Canada) - français (Canada)",
            "fr-FR" => "French (France) - français (France)",
            "fr-CH" => "French (Switzerland) - français (Suisse)",
            "gl" => "Galician - galego",
            "ka" => "Georgian - ქართული",
            "de" => "German - Deutsch",
            "de-AT" => "German (Austria) - Deutsch (Österreich)",
            "de-DE" => "German (Germany) - Deutsch (Deutschland)",
            "de-LI" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "de-CH" => "German (Switzerland) - Deutsch (Schweiz)",
            "el" => "Greek - Ελληνικά",
            "gn" => "Guarani",
            "gu" => "Gujarati - ગુજરાતી",
            "ha" => "Hausa",
            "haw" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "he" => "Hebrew - עברית",
            "hi" => "Hindi - हिन्दी",
            "hu" => "Hungarian - magyar",
            "is" => "Icelandic - íslenska",
            "id" => "Indonesian - Indonesia",
            "ia" => "Interlingua",
            "ga" => "Irish - Gaeilge",
            "it" => "Italian - italiano",
            "it-IT" => "Italian (Italy) - italiano (Italia)",
            "it-CH" => "Italian (Switzerland) - italiano (Svizzera)",
            "ja" => "Japanese - 日本語",
            "kn" => "Kannada - ಕನ್ನಡ",
            "kk" => "Kazakh - қазақ тілі",
            "km" => "Khmer - ខ្មែរ",
            "ko" => "Korean - 한국어",
            "ku" => "Kurdish - Kurdî",
            "ky" => "Kyrgyz - кыргызча",
            "lo" => "Lao - ລາວ",
            "la" => "Latin",
            "lv" => "Latvian - latviešu",
            "ln" => "Lingala - lingála",
            "lt" => "Lithuanian - lietuvių",
            "mk" => "Macedonian - македонски",
            "ms" => "Malay - Bahasa Melayu",
            "ml" => "Malayalam - മലയാളം",
            "mt" => "Maltese - Malti",
            "mr" => "Marathi - मराठी",
            "mn" => "Mongolian - монгол",
            "ne" => "Nepali - नेपाली",
            "no" => "Norwegian - norsk",
            "nb" => "Norwegian Bokmål - norsk bokmål",
            "nn" => "Norwegian Nynorsk - nynorsk",
            "oc" => "Occitan",
            "or" => "Oriya - ଓଡ଼ିଆ",
            "om" => "Oromo - Oromoo",
            "ps" => "Pashto - پښتو",
            "fa" => "Persian - فارسی",
            "pl" => "Polish - polski",
            "pt" => "Portuguese - português",
            "pt-BR" => "Portuguese (Brazil) - português (Brasil)",
            "pt-PT" => "Portuguese (Portugal) - português (Portugal)",
            "pa" => "Punjabi - ਪੰਜਾਬੀ",
            "qu" => "Quechua",
            "ro" => "Romanian - română",
            "mo" => "Romanian (Moldova) - română (Moldova)",
            "rm" => "Romansh - rumantsch",
            "ru" => "Russian - русский",
            "gd" => "Scottish Gaelic",
            "sr" => "Serbian - српски",
            "sh" => "Serbo-Croatian - Srpskohrvatski",
            "sn" => "Shona - chiShona",
            "sd" => "Sindhi",
            "si" => "Sinhala - සිංහල",
            "sk" => "Slovak - slovenčina",
            "sl" => "Slovenian - slovenščina",
            "so" => "Somali - Soomaali",
            "st" => "Southern Sotho",
            "es" => "Spanish - español",
            "es-AR" => "Spanish (Argentina) - español (Argentina)",
            "es-419" => "Spanish (Latin America) - español (Latinoamérica)",
            "es-MX" => "Spanish (Mexico) - español (México)",
            "es-ES" => "Spanish (Spain) - español (España)",
            "es-US" => "Spanish (United States) - español (Estados Unidos)",
            "su" => "Sundanese",
            "sw" => "Swahili - Kiswahili",
            "sv" => "Swedish - svenska",
            "tg" => "Tajik - тоҷикӣ",
            "ta" => "Tamil - தமிழ்",
            "tt" => "Tatar",
            "te" => "Telugu - తెలుగు",
            "th" => "Thai - ไทย",
            "ti" => "Tigrinya - ትግርኛ",
            "to" => "Tongan - lea fakatonga",
            "tr" => "Turkish - Türkçe",
            "tk" => "Turkmen",
            "tw" => "Twi",
            "uk" => "Ukrainian - українська",
            "ur" => "Urdu - اردو",
            "ug" => "Uyghur",
            "uz" => "Uzbek - o‘zbek",
            "vi" => "Vietnamese - Tiếng Việt",
            "wa" => "Walloon - wa",
            "cy" => "Welsh - Cymraeg",
            "fy" => "Western Frisian",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba - Èdè Yorùbá",
            "zu" => "Zulu - isiZulu",
        );
        return array_key_exists($key, $languages) ? $languages[$key] : $key;
    }
    public static function pagination_limit()
    {
        $pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
        return (int)$pagination_limit->value;
    }

    public static function remove_invalid_charcaters($str)
    {
        return str_ireplace(['\'', '"', ';', '<', '>', '?'], ' ', $str);
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $oldValue = env($envKey);
        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    public static function requestSender($request): array
    {
        $remove = array("http://", "https://", "www.");
        $url = str_replace($remove, "", url('/'));

        $post = [
            base64_decode('dXNlcm5hbWU=') => $request['username'],//un
            base64_decode('cHVyY2hhc2Vfa2V5') => $request['purchase_key'],//pk
            base64_decode('c29mdHdhcmVfaWQ=') => base64_decode(env(base64_decode('Mzk4MjcwMTE='))),//sid
            base64_decode('ZG9tYWlu') => $url,
        ];

        //session()->put('domain', 'https://' . preg_replace("#^[^:/.]*[:/]+#i", "", $request['domain']));

        $ch = curl_init('https://check.6amtech.com/api/v1/domain-register');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);

        try {
            if (base64_decode(json_decode($response, true)['active'])) {
                return [
                    'active' => (int)base64_decode(json_decode($response, true)['active'])
                ];
            }
            return [
                'active' => 0
            ];
        } catch (\Exception $exception) {
            return [
                'active' => 1
            ];
        }
    }

    public static function remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") Helpers::remove_dir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function module_permission_check($mod_name)
    {
        return self::admin_has_module($mod_name);
    }

    public static function admin_has_module(string $mod_name): bool
    {
        if (!auth('admin')->check() || !auth('admin')->user()) {
            return false;
        }

        if (auth('admin')->user()->role_id == 1) {
            return true;
        }

        $permission = auth('admin')->user()->role?->modules;

        return isset($permission) && in_array($mod_name, (array) json_decode($permission), true);
    }

    public static function admin_has_any_module(array $modules): bool
    {
        foreach ($modules as $module) {
            if (self::admin_has_module($module)) {
                return true;
            }
        }

        return false;
    }

    public static function returns_user_can_manage_ops(): bool
    {
        return self::admin_has_any_module([
            'returns_queue_section',
            'returns_ops_board_section',
            'returns_playbooks_section',
        ]);
    }

    public static function returns_user_is_guest_demo(): bool
    {
        if (!auth('admin')->check() || !auth('admin')->user()) {
            return false;
        }

        return strtolower((string) auth('admin')->user()->role?->name) === 'guest demo';
    }

    public static function returns_user_is_inspector(): bool
    {
        return self::admin_has_module('returns_inspect_section')
            && self::admin_has_module('returns_cases_section')
            && !self::returns_user_can_manage_ops();
    }

    public static function returns_user_can_update_decision_queue(): bool
    {
        return self::admin_has_module('returns_queue_section')
            && !self::returns_user_is_guest_demo();
    }

    public static function returns_user_can_edit_playbooks(): bool
    {
        return self::admin_has_module('returns_playbooks_section')
            && !self::returns_user_is_guest_demo();
    }

    public static function returns_user_can_view_review_requests(): bool
    {
        return self::admin_has_module('returns_ops_board_section')
            && !self::returns_user_is_inspector()
            && !self::returns_user_is_guest_demo();
    }

    public static function returns_home_route(): string
    {
        if (self::returns_user_is_inspector()) {
            return route('admin.returns.inspect');
        }

        if (self::admin_has_module('returns_ops_board_section')) {
            return route('admin.returns.dashboard.index');
        }

        if (self::admin_has_module('returns_queue_section')) {
            return route('admin.returns.queue.index');
        }

        if (self::admin_has_module('returns_cases_section')) {
            return route('admin.returns.cases.index');
        }

        return route('admin.returns.inspect');
    }

    public static function paginateValueNumberOptions(?int $custom = null): array
    {
        $allowedNumberOptions = [5, 10, 20, 30, 40, 50, 100, self::pagination_limit()];

        if ($custom) {
            $allowedNumberOptions[] = (int) $custom;
        }

        $uniqueAllowedNumberOptions = array_unique($allowedNumberOptions);
        sort($uniqueAllowedNumberOptions);

        return $uniqueAllowedNumberOptions;
    }
}
//for translation
function translate(string $key, array $replace = [], ?string $locale = null): array|string|Translator|null
{
    $locale = $locale ?? app()->getLocale();
    $normalizedKey = Helpers::remove_invalid_charcaters($key);

    try {
        $langFilePath = base_path("resources/lang/$locale/messages.php");
        $translations = include $langFilePath;

        $defaultValue = ucfirst(str_replace('_', ' ', $normalizedKey));
        $translatedValue = str_replace(['{', '}'], [':', ''], $defaultValue);

        if (!array_key_exists($normalizedKey, $translations)) {
            $translations[$normalizedKey] = $translatedValue;

            $exported = "<?php return " . var_export($translations, true) . ";";
            file_put_contents($langFilePath, $exported);
            $translation = $translations[$normalizedKey];
            foreach ($replace as $k => $v) {
                $translation = str_replace(":$k", $v, $translation);
            }
            return $translation;
        }
        return trans("messages.$normalizedKey", $replace, $locale);
    } catch (\Exception) {
        return trans("messages.$normalizedKey", $replace, $locale);
    }
}

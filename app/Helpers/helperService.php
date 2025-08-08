<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Country;
use App\Models\Provincy;
use App\Models\City;
use App\Models\Sector;
use App\Models\Coordinator;

function validateEmail($email) {
    $pattern = "/^(?=.*[0-9])(?=.*[+])[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    return preg_match($pattern, $email);
}

function firstCapitalize($teks) {
    return ucwords(strtolower($teks));
}

function isActiveRoute($routeName) {
    return request()->routeIs($routeName) ? 'active' : '';
}

function fileGetType($files)
{
    $ret = [];

    if (!empty($files)) {
        $infoPath = strtolower(pathinfo(public_path($files), PATHINFO_EXTENSION));

        $fileTypes = [
            'gambar' => ['extensions' => ['jpg', 'png', 'jpeg', 'svg'], 'thumbnail' => Storage::url($files)],
            //asset('storage/' . $files)],
            'video' => ['extensions' => ['mp4', 'mkv', 'avi'], 'thumbnail' => asset('assets/media/extension/video.png')],
            'excel' => ['extensions' => ['xls', 'xlsx'], 'thumbnail' => asset('assets/media/extension/xlsx.png')],
            'powerpoint' => ['extensions' => ['ppt', 'pptx'], 'thumbnail' => asset('assets/media/extension/ppt.png')],
            'word' => ['extensions' => ['doc', 'docx'], 'thumbnail' => asset('assets/media/extension/docx.png')],
            'pdf' => ['extensions' => ['pdf', 'epub'], 'thumbnail' => asset('assets/media/extension/pdf.png')],
        ];

        $typeText = 'Lain-Lain';
        $thumbnail = asset('assets/media/extension/file.png');

        foreach ($fileTypes as $type => $data) {
            if (in_array($infoPath, $data['extensions'])) {
                $typeText = ucfirst($type);
                $thumbnail = $data['thumbnail'];
                break;
            }
        }

        $ret = [
            'url' => Storage::url($files) ?? asset('storage/' . $files),
            'thumbnail' => $thumbnail,
            'extension' => $infoPath,
            'type_text' => $typeText,
            'file' => $files,
        ];
    }

    return $ret;
}

function fileArrayGetType($files)
{
    $ret = [];

    if (empty($files)) {
        return $ret;
    }

    $fileTypes = [
        'gambar' => ['extensions' => ['jpg', 'png', 'jpeg', 'svg']],
        'video' => ['extensions' => ['mp4', 'mkv', 'avi'], 'thumbnail' => asset('assets/media/extension/video.png')],
        'excel' => ['extensions' => ['xls', 'xlsx'], 'thumbnail' => asset('assets/media/extension/xlsx.png')],
        'powerpoint' => ['extensions' => ['ppt', 'pptx'], 'thumbnail' => asset('assets/media/extension/ppt.png')],
        'word' => ['extensions' => ['doc', 'docx'], 'thumbnail' => asset('assets/media/extension/docx.png')],
        'pdf' => ['extensions' => ['pdf', 'epub'], 'thumbnail' => asset('assets/media/extension/pdf.png')],
    ];

    foreach ($files as $file) {
        $infoPath = strtolower(pathinfo(public_path($file), PATHINFO_EXTENSION));

        $typeText = 'Lain-Lain';
        $thumbnail = asset('assets/media/extension/file.png');

        foreach ($fileTypes as $type => $data) {
            if (in_array($infoPath, $data['extensions'])) {
                $typeText = ucfirst($type);

                if ($type === 'gambar') {
                    $thumbnail = Storage::url($file);
                } else {
                    $thumbnail = $data['thumbnail'];
                }
                break;
            }
        }

        $ret[] = [
            'url' => Storage::url($file),
            'thumbnail' => $thumbnail,
            'extension' => $infoPath,
            'type_text' => $typeText,
            'file' => $file,
        ];
    }

    return $ret;
}

function generateRandomString($length) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';  // Huruf a - z dan angka 0 - 9
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function formatSetTimezone($data)
{
    return Carbon::parse($data)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i');
}

function formatOnlyDate($data)
{
    return Carbon::parse($data)->setTimezone('Asia/Jakarta')->format('Y-m-d');
}

function formatIndonesiaDateTime($dateTime) {
    return Carbon::parse($dateTime)->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y');
}

function formatIndonesiaMonthYear($dateTime) {
    return Carbon::parse($dateTime)->timezone('Asia/Jakarta')->locale('id')->translatedFormat('F Y');
}

function formatdateChange($date, $format)
{
    $data = date($format, strtotime($date));

    return $data;
}

function formatToDateYmd($inputDate) {
    $inputDate = preg_replace('/\s*GMT[+-]\d{4}\s*\(.*\)/', '', $inputDate);

    $formats = [
        'Y-m-d',       // Format standar Y-m-d
        'd/m/Y',       // Format d/m/Y
        'm/Y',         // Format MM/YYYY
        'M Y',         // Format bulan singkat dan tahun (Jan 2024)
        'D M d Y H:i:s', // Format full date with day name (Thu Oct 03 2024 00:00:00)
        'd F Y',       // Format seperti 1 Januari 2024
        'd-m-Y',       // Format d-m-Y
        'Y-m-d H:i:s', // Format dengan jam
        'Y',
    ];

    foreach ($formats as $format) {
        $dateTime = DateTime::createFromFormat($format, trim($inputDate));
        if ($dateTime !== false) {
            return $dateTime->format('Y-m-d');
        }
    }

    $timestamp = strtotime($inputDate);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }

    return null;
}

function rangeLimit($value, $rangeType)
{
    switch ($rangeType) {
        case 'hour':
            $startDate = date('Y-m-d H:i:s', strtotime("+{$value} hours"));
            break;
        case 'minute':
            $startDate = date('Y-m-d H:i:s', strtotime("+{$value} minutes"));
            break;
        case 'day':
            $startDate = date('Y-m-d H:i:s', strtotime("+{$value} days"));
            break;
        case 'week':
            $startDate = date('Y-m-d H:i:s', strtotime("+{$value} weeks"));
            break;
        case 'month':
            $startDate = date('Y-m-d H:i:s', strtotime("+{$value} months"));
            break;
        case 'year':
            $startDate = date('Y-m-d H:i:s', strtotime("+{$value} years"));
            break;
        default:
            throw new Exception("Invalid range type: {$rangeType}");
    }

    return $startDate ?? date('Y-m-d H:i:s');
}

function diffHumans($timestamp)
{
    return Carbon::parse($timestamp)->timezone('Asia/Jakarta')->diffForHumans();
}

function customSlug($name, $code)
{
    $firstWord = explode(' ', $name)[0];
    
    return strtoupper($firstWord) . '-' . strtoupper($code);
}

function replaceName($name) {
    $name = preg_replace('/\s+/', ' ', trim($name));
    $name = strtolower(str_replace(' ', '-', $name));
    $name = preg_replace('/[^a-z0-9\-]/', '', $name);
    
    return $name;
}

function formatTitleCase($value)
{
    if (empty($value)) {
        return null;
    }

    $value = str_replace(['-', '_'], ' ', $value);

    return ucwords(strtolower($value));
}

function getPriceFormatted($price): string
{
    $price = is_numeric($price) ? (float) $price : 0;

    if ($price == 0) {
        return '0';
    }

    return number_format($price, 0, ',', '.');
}

function getPeriod($start, $end): string
{
    return Carbon::parse($start)->format('d M Y') . ' - ' . carbon::parse($end)->format('d M Y');
}


function listCountries()
{
    return Cache::rememberForever('list_countries', function () {
        return Country::where('is_published', true)->pluck('name', 'id')->toArray();
    });
}

function listProvinces()
{
    return Cache::rememberForever('list_provinces', function () {
        $exceptions = [
            'Dki JAKARTA' => 'DKI Jakarta',
        ];

        return Provincy::where('is_published', true)
            ->get()
            ->mapWithKeys(function ($item) use ($exceptions) {
                $name = ucwords(strtolower($item->name));
                $name = $exceptions[$name] ?? $name;

                return [$item->code => $name];
            })
            ->toArray();
    });
}

function listCities($provinceCode = null)
{
    $cacheKey = $provinceCode
        ? "cities_by_province_{$provinceCode}"
        : "cities_all";

    return Cache::rememberForever($cacheKey, function () use ($provinceCode) {
        $query = City::where('is_published', true);

        if ($provinceCode) {
            $query->where('province_code', $provinceCode);
        }

        return $query->pluck('name')->map(function ($name) {
            return ucfirst($name);
        })->toArray();
    });
}

function listCitiId($provinceCode = null)
{
    $cacheKey = $provinceCode
        ? "cities_Id_{$provinceCode}"
        : "cities";

    return Cache::rememberForever($cacheKey, function () use ($provinceCode) {
        $query = City::where('is_published', true);

        if ($provinceCode) {
            $query->where('province_code', $provinceCode);
        }

        return $query->pluck('name', 'id')->map(function ($name) {
            return ucwords(strtolower($name));
        })->toArray();
    });
}

function cities()
{
    return Cache::rememberForever('indonesia_cities_mapped', function () {
        return DB::table('indonesia_cities')
            ->select('id', 'prefix', 'name')
            ->orderBy('prefix')
            ->get()
            ->map(function ($city) {
                return [
                    'id'     => $city->id,
                    'prefix' => $city->prefix,
                    'name'   => ucwords(strtolower($city->name)),
                ];
            })
            ->values();
    });
}

function getCityByIdData($id)
{
    $data = City::find($id);

    return $data;
}

function getCityById($id)
{
    $data = City::find($id);
    $name = $data->name ?? '';

    return ucwords(strtolower($name));
}

function getProvienceById($id)
{
    $data = Provincy::where('code', $id)->first();
    $name = $data->name ?? '';

    return ucwords(strtolower($name));
}

function withPlaceholder(array $options, string $placeholder = '-- Select --')
{
    return ['' => $placeholder] + $options;
}

function listCountryMapping()
{
    $data = Country::where('is_published', true)
            ->orderByRaw("CASE WHEN phonecode = '62' THEN 0 ELSE 1 END")
            ->orderBy('order', 'ASC')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->phonecode => $item->name];
            });

    return $data;
}

function formatNumber($value): string
{
    if (!is_numeric($value)) {
        return '0';
    }

    return number_format((int) $value, 0, ',', '.');
}

function listSector()
{
    return Sector::all();
}

function listCoordinator()
{
    return Coordinator::all();
}

function masterName($master, $code) 
{
    $data = $master::where('code', $code)->first();

    return $data ? ucwords(strtolower($data->name)) : null;
}

?>
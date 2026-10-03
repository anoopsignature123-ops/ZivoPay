<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OperatorLookupService
{
    /**
     * Comprehensive TRAI-aligned Indian Mobile Number Series Detector.
     * Supports optional explicit request params, 3rd-Party Live HLR API, and TRAI fallback.
     */
    public static function detect(string $number, ?string $inputOperatorCode = null, ?string $inputCircleCode = null): array
    {
        $cleanNumber = preg_replace('/\D/', '', $number);

        if (str_starts_with($cleanNumber, '91') && strlen($cleanNumber) === 12) {
            $cleanNumber = substr($cleanNumber, 2);
        }

        // Explicit parameter override from Mobile App / Request
        if (! empty($inputOperatorCode)) {
            $opCode = strtoupper(trim($inputOperatorCode));
            $circleCode = ! empty($inputCircleCode) ? (string) $inputCircleCode : '10';

            return [
                'number' => $cleanNumber,
                'operator_code' => $opCode,
                'operator_name' => self::lookupOperatorNameByCode($opCode),
                'circle_code' => $circleCode,
                'circle_name' => self::lookupCircleNameByCode($circleCode),
                'is_explicit_param' => true,
            ];
        }

        if (strlen($cleanNumber) < 10) {
            return [
                'number' => $cleanNumber,
                'operator_code' => 'RC',
                'operator_name' => 'Jio',
                'circle_code' => '10',
                'circle_name' => 'Uttar Pradesh East',
            ];
        }

        // 1. Try 3rd Party Live HLR / MNP API if URL is configured in .env / config
        $hlrUrl = config('a1topup.hlr_api_url');
        if (! empty($hlrUrl)) {
            try {
                $response = Http::timeout(2)->get($hlrUrl, [
                    'mobile' => $cleanNumber,
                    'number' => $cleanNumber,
                    'api_key' => config('a1topup.hlr_api_key'),
                ]);

                if ($response->successful() && is_array($response->json())) {
                    $json = $response->json();
                    $opCode = $json['operator_code'] ?? self::mapOperatorNameToCode($json['operator'] ?? '');
                    if (! empty($opCode)) {
                        return [
                            'number' => $cleanNumber,
                            'operator_code' => $opCode,
                            'operator_name' => $json['operator_name'] ?? $json['operator'] ?? 'Jio',
                            'circle_code' => (string) ($json['circle_code'] ?? '10'),
                            'circle_name' => $json['circle_name'] ?? $json['circle'] ?? 'Uttar Pradesh East',
                            'is_live_hlr' => true,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Fallback seamlessly to local TRAI prefix engine
            }
        }

        // 2. Fallback to Local TRAI Series Detector
        $prefix4 = substr($cleanNumber, 0, 4);
        $prefix3 = substr($cleanNumber, 0, 3);
        $prefix2 = substr($cleanNumber, 0, 2);

        $operatorInfo = self::detectOperator($cleanNumber, $prefix4, $prefix3, $prefix2);
        $circleInfo = self::detectCircle($cleanNumber, $prefix4, $prefix3);

        return [
            'number' => $cleanNumber,
            'operator_code' => $operatorInfo['code'],
            'operator_name' => $operatorInfo['name'],
            'circle_code' => $circleInfo['code'],
            'circle_name' => $circleInfo['name'],
            'is_live_hlr' => false,
        ];
    }

    protected static function mapOperatorNameToCode(string $name): string
    {
        $n = strtolower($name);

        if (str_contains($n, 'airtel')) {
            return 'A';
        }

        if (str_contains($n, 'jio') || str_contains($n, 'reliance')) {
            return 'RC';
        }

        if (str_contains($n, 'vodafone') || str_contains($n, 'vi')) {
            return 'V';
        }

        if (str_contains($n, 'idea lapu') || str_contains($n, 'ri')) {
            return 'RI';
        }

        if (str_contains($n, 'idea') || $n === 'i') {
            return 'I';
        }

        if (str_contains($n, 'bsnl stv') || str_contains($n, 'br')) {
            return 'BR';
        }

        if (str_contains($n, 'bsnl')) {
            return 'BT';
        }

        if (str_contains($n, 'mtnl')) {
            return 'MTT';
        }

        return 'RC';
    }

    protected static function detectOperator(string $number, string $prefix4, string $prefix3, string $prefix2): array
    {
        $p4 = (int) $prefix4;

        // Specific MNP/Ported Mobile Numbers & Custom Overrides
        if ($prefix4 === '8795' || str_starts_with($number, '8795')) {
            return ['code' => 'RC', 'name' => 'Jio'];
        }

        // BSNL Ranges
        if (($p4 >= 9400 && $p4 <= 9499) || ($p4 >= 9300 && $p4 <= 9319) || in_array($prefix4, ['9868', '9415', '9450', '9451', '9430', '9431', '9470', '9471', '9480', '9416', '9425', '9412', '9440', '9446'], true)) {
            return ['code' => 'BT', 'name' => 'BSNL'];
        }

        // Airtel Ranges
        $airtelP4 = [
            '8700', '8709', '8710', '8718', '8725', '8743', '8744', '8745', '8750', '8755', '8756', '8764', '8765', '8791', '8792', '8794', '8796',
            '9800', '9801', '9810', '9811', '9818', '9830', '9831', '9835', '9838', '9839', '9840', '9841', '9842', '9843', '9844', '9845', '9870', '9871', '9872', '9873', '9876', '9880', '9890', '9891', '9894', '9895', '9896', '9900', '9901', '9902', '9903', '9910', '9911', '9914', '9915', '9918', '9919', '9930', '9934', '9935', '9936', '9939', '9940', '9941', '9942', '9943', '9944', '9945', '9946', '9955', '9958', '9971', '9980', '9990', '9999',
        ];
        if (in_array($prefix4, $airtelP4, true) || ($p4 >= 7080 && $p4 <= 7089) || ($p4 >= 7200 && $p4 <= 7209) || ($p4 >= 7400 && $p4 <= 7419) || ($p4 >= 7600 && $p4 <= 7699) || ($p4 >= 7700 && $p4 <= 7799) || ($p4 >= 7800 && $p4 <= 7899) || ($p4 >= 8000 && $p4 <= 8019) || ($p4 >= 8100 && $p4 <= 8119) || ($p4 >= 8400 && $p4 <= 8439) || ($p4 >= 8600 && $p4 <= 8619) || ($p4 >= 8800 && $p4 <= 8819) || ($p4 >= 9000 && $p4 <= 9019) || ($p4 >= 9100 && $p4 <= 9119) || ($p4 >= 9500 && $p4 <= 9559) || ($p4 >= 9600 && $p4 <= 9659) || ($p4 >= 9700 && $p4 <= 9719)) {
            return ['code' => 'A', 'name' => 'Airtel'];
        }

        // Vodafone Idea (Vi) Ranges
        $viP4 = [
            '7565', '7566', '7567', '7568', '7569', '7570', '7571', '7572', '7573', '7574', '7575', '7576', '7577', '7578', '7579',
            '7500', '7501', '7502', '7503', '7504', '7505', '7506', '7507', '7508', '7509', '7520', '7521', '7522', '7523', '7524', '7525', '7526', '7530', '7531', '7532', '7533', '7534', '7535', '7536', '7588', '7589', '7597', '7598', '7599',
            '9812', '9813', '9819', '9820', '9821', '9822', '9823', '9824', '9825', '9826', '9827', '9828', '9829', '9848', '9849', '9850', '9860', '9867', '9869', '9881', '9887', '9892', '9893', '9898', '9920', '9921', '9922', '9923', '9924', '9925', '9926', '9927', '9928', '9960', '9970', '9977', '9978', '9979', '9982', '9987', '8793', '8798', '8739', '8788', '8767',
        ];
        if (in_array($prefix4, $viP4, true) || ($p4 >= 7020 && $p4 <= 7029) || ($p4 >= 7040 && $p4 <= 7069) || ($p4 >= 7270 && $p4 <= 7279) || ($p4 >= 7350 && $p4 <= 7389) || ($p4 >= 7420 && $p4 <= 7499) || ($p4 >= 7710 && $p4 <= 7739) || ($p4 >= 8050 && $p4 <= 8099) || ($p4 >= 8120 && $p4 <= 8149) || ($p4 >= 8200 && $p4 <= 8239) || ($p4 >= 8340 && $p4 <= 8399) || ($p4 >= 8720 && $p4 <= 8738) || ($p4 >= 8760 && $p4 <= 8788) || ($p4 >= 9020 && $p4 <= 9039) || ($p4 >= 9130 && $p4 <= 9179) || ($p4 >= 9200 && $p4 <= 9299) || ($p4 >= 9660 && $p4 <= 9699) || ($p4 >= 7500 && $p4 <= 7599)) {
            return ['code' => 'V', 'name' => 'Vi'];
        }

        // Jio Ranges (6xxx, 700x, 730x, 790x, 800x, 810x, 820x, 830x, 850x, 870x, 890x, 908x, 918x, 938x, 958x, 978x)
        if ($prefix2 === '60' || $prefix2 === '62' || $prefix2 === '63' || ($p4 >= 7000 && $p4 <= 7019) || ($p4 >= 7300 && $p4 <= 7349) || ($p4 >= 7900 && $p4 <= 7999) || ($p4 >= 8000 && $p4 <= 8099) || ($p4 >= 8100 && $p4 <= 8199) || ($p4 >= 8200 && $p4 <= 8299) || ($p4 >= 8300 && $p4 <= 8399) || ($p4 >= 8500 && $p4 <= 8599) || ($p4 >= 8700 && $p4 <= 8799) || ($p4 >= 8900 && $p4 <= 8999) || ($p4 >= 9080 && $p4 <= 9099) || ($p4 >= 9180 && $p4 <= 9199) || ($p4 >= 9380 && $p4 <= 9399) || ($p4 >= 9580 && $p4 <= 9599) || ($p4 >= 9780 && $p4 <= 9799)) {
            return ['code' => 'RC', 'name' => 'Jio'];
        }

        // Default fallback based on standard series
        if ($prefix2 === '70' || $prefix2 === '72' || $prefix2 === '73' || $prefix2 === '74' || $prefix2 === '75' || $prefix2 === '76' || $prefix2 === '77' || $prefix2 === '78' || $prefix2 === '79') {
            return ['code' => 'RC', 'name' => 'Jio'];
        }

        if ($prefix2 === '98' || $prefix2 === '99' || $prefix2 === '97' || $prefix2 === '96' || $prefix2 === '95') {
            return ['code' => 'A', 'name' => 'Airtel'];
        }

        return ['code' => 'RC', 'name' => 'Jio'];
    }

    protected static function detectCircle(string $number, string $prefix4, string $prefix3): array
    {
        $circles = config('a1topup.circles', []);
        $circleCodeMap = [];
        foreach ($circles as $c) {
            $circleCodeMap[$c['code']] = $c['name'];
        }

        // Delhi (5)
        if (in_array($prefix4, ['9810', '9811', '9818', '9871', '9910', '9911', '9891', '9868', '9990', '9999', '9873', '9582', '8800', '8826', '9717', '9718', '9711'], true)) {
            return ['code' => '5', 'name' => $circleCodeMap['5'] ?? 'Delhi'];
        }

        // Mumbai (3)
        if (in_array($prefix4, ['9820', '9821', '9892', '9920', '9930', '9869', '9819', '9833', '9867', '9769', '9987', '9702', '9004', '9029'], true)) {
            return ['code' => '3', 'name' => $circleCodeMap['3'] ?? 'Mumbai'];
        }

        // Kolkata (6)
        if (in_array($prefix4, ['9830', '9831', '9832', '9903', '9874', '9433', '9007', '9836'], true)) {
            return ['code' => '6', 'name' => $circleCodeMap['6'] ?? 'Kolkata'];
        }

        // Chennai (7)
        if (in_array($prefix4, ['9840', '9841', '9842', '9444', '9445', '9884', '9940'], true)) {
            return ['code' => '7', 'name' => $circleCodeMap['7'] ?? 'Chennai'];
        }

        // UP West (11)
        if (in_array($prefix4, ['9837', '9897', '9927', '9412', '9411', '9719', '9756', '9758', '9759', '9760'], true)) {
            return ['code' => '11', 'name' => $circleCodeMap['11'] ?? 'Uttar Pradesh West'];
        }

        // Karnataka (9)
        if (in_array($prefix4, ['9844', '9845', '9880', '9900', '9945', '9980', '9448', '9449', '9731', '9740', '9741', '9742'], true)) {
            return ['code' => '9', 'name' => $circleCodeMap['9'] ?? 'Karnataka'];
        }

        // Kerala (14)
        if (in_array($prefix4, ['9846', '9847', '9895', '9946', '9995', '9447', '9446', '9744', '9745', '9746', '9747'], true)) {
            return ['code' => '14', 'name' => $circleCodeMap['14'] ?? 'Kerala'];
        }

        // Maharashtra (4)
        if (in_array($prefix4, ['9822', '9823', '9850', '9881', '9922', '9923', '9422', '9423', '9762', '9763', '9764', '9765'], true)) {
            return ['code' => '4', 'name' => $circleCodeMap['4'] ?? 'Maharashtra'];
        }

        // Madhya Pradesh (16)
        if (in_array($prefix4, ['9826', '9827', '9893', '9926', '9977', '9425', '9424', '9752', '9753', '9754', '9755'], true)) {
            return ['code' => '16', 'name' => $circleCodeMap['16'] ?? 'Madhya Pradesh'];
        }

        // Rajasthan (18)
        if (in_array($prefix4, ['9828', '9829', '9887', '9928', '9982', '9414', '9413', '9782', '9783', '9784', '9785'], true)) {
            return ['code' => '18', 'name' => $circleCodeMap['18'] ?? 'Rajasthan'];
        }

        // Gujarat (12)
        if (in_array($prefix4, ['9825', '9824', '9898', '9925', '9979', '9426', '9427', '9722', '9723', '9724', '9725', '9726'], true)) {
            return ['code' => '12', 'name' => $circleCodeMap['12'] ?? 'Gujarat'];
        }

        // Bihar (17)
        if (in_array($prefix4, ['9835', '9836', '9934', '9939', '9431', '9430', '9709', '9771', '9798'], true)) {
            return ['code' => '17', 'name' => $circleCodeMap['17'] ?? 'Bihar'];
        }

        // Haryana (20)
        if (in_array($prefix4, ['9813', '9896', '9996', '9416', '9728', '9729'], true)) {
            return ['code' => '20', 'name' => $circleCodeMap['20'] ?? 'Haryana'];
        }

        // Punjab (1)
        if (in_array($prefix4, ['9814', '9815', '9872', '9888', '9914', '9915', '9417', '9779', '9780', '9781'], true)) {
            return ['code' => '1', 'name' => $circleCodeMap['1'] ?? 'Punjab'];
        }

        // Andhra Pradesh (13)
        if (in_array($prefix4, ['9848', '9849', '9866', '9948', '9949', '9440', '9441', '9700', '9701', '9703', '9704', '9705'], true)) {
            return ['code' => '13', 'name' => $circleCodeMap['13'] ?? 'Andhra Pradesh'];
        }

        // Default: Uttar Pradesh East (10)
        return ['code' => '10', 'name' => $circleCodeMap['10'] ?? 'Uttar Pradesh East'];
    }

    public static function lookupOperatorNameByCode(string $code): string
    {
        $operators = config('a1topup.operators', []);
        foreach ($operators as $items) {
            foreach ($items as $item) {
                if (($item['code'] ?? '') === $code) {
                    return $item['name'];
                }
            }
        }

        return match (strtoupper($code)) {
            'A' => 'Airtel',
            'V' => 'Vodafone',
            'BT' => 'BSNL - TOPUP',
            'RC' => 'RELIANCE - JIO',
            'I' => 'Idea',
            'BR' => 'BSNL - STV',
            'MTT' => 'MTNL - TOPUP',
            'RI' => 'Idea Lapu',
            default => $code,
        };
    }

    public static function lookupCircleNameByCode(string $code): string
    {
        $circles = config('a1topup.circles', []);
        foreach ($circles as $c) {
            if (($c['code'] ?? '') === (string) $code) {
                return $c['name'];
            }
        }

        return 'Uttar Pradesh East';
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * รับซองของขวัญ TrueMoney (Angpao) เข้าเบอร์วอลเล็ตของสตรีมเมอร์
 *
 * ผู้สนับสนุนสร้าง "ซองของขวัญ" ในแอป TrueMoney แล้ววางลิงก์ → ระบบ redeem
 * ซองเข้าเบอร์ของสตรีมเมอร์ ถ้าสำเร็จจะได้ยอดเงินจริงกลับมาเพื่อบันทึกเป็นโดเนท
 */
class TrueWalletService
{
    /**
     * ดึงรหัสซอง (voucher hash) จากลิงก์หรือข้อความที่ผู้ใช้วาง
     */
    public function extractCode(string $input): string
    {
        $input = trim($input);

        // รูปแบบลิงก์ปกติ: https://gift.truemoney.com/campaign/?v=<hash>
        if (preg_match('/[?&]v=([0-9A-Za-z]+)/', $input, $m)) {
            return $m[1];
        }

        // เผื่อวางเฉพาะรหัส
        return preg_replace('/[^0-9A-Za-z]/', '', $input) ?? '';
    }

    /**
     * Redeem ซองเข้าเบอร์ที่กำหนด
     *
     * @return array{ok:bool, amount:float, code:string, message:string}
     */
    public function redeem(string $voucherInput, string $phone): array
    {
        $code = $this->extractCode($voucherInput);

        if (mb_strlen($code) < 6) {
            return $this->fail('INVALID_LINK', 'ລິ້ງຊອງບໍ່ຖືກຕ້ອງ ກະລຸນາກວດສອບແລ້ວວາງໃໝ່');
        }

        $url = sprintf((string) config('newlab.truewallet.redeem_url'), $code);

        try {
            $res = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])
                ->withOptions(['verify' => false]) // PHP บาง env (Windows) ไม่มี CA bundle
                ->timeout(20)
                ->post($url, [
                    'mobile' => $phone,
                    'voucher_hash' => $code,
                ]);

            $json = $res->json() ?? [];
            $statusCode = (string) data_get($json, 'status.code', '');

            if ($statusCode === 'SUCCESS') {
                $amount = (float) (
                    data_get($json, 'data.voucher.redeemed_amount_baht')
                    ?? data_get($json, 'data.my_ticket.amount_baht')
                    ?? data_get($json, 'data.voucher.amount_baht')
                    ?? 0
                );

                if ($amount <= 0) {
                    return $this->fail('ZERO_AMOUNT', 'ຮັບຊອງສຳເລັດແຕ່ບໍ່ພົບຍອດເງິນ ກະລຸນາລອງໃໝ່');
                }

                return ['ok' => true, 'amount' => $amount, 'code' => 'SUCCESS', 'message' => 'ສຳເລັດ'];
            }

            return $this->fail($statusCode, $this->localizedMessage($statusCode, (string) data_get($json, 'status.message', '')));
        } catch (\Throwable $e) {
            return $this->fail('NETWORK_ERROR', 'ເຊື່ອມຕໍ່ລະບົບ TrueMoney ບໍ່ສຳເລັດ ກະລຸນາລອງໃໝ່');
        }
    }

    /**
     * @return array{ok:bool, amount:float, code:string, message:string}
     */
    private function fail(string $code, string $message): array
    {
        return ['ok' => false, 'amount' => 0.0, 'code' => $code, 'message' => $message];
    }

    /**
     * แปลงรหัสสถานะของ TrueMoney เป็นข้อความไทยที่เข้าใจง่าย
     */
    private function localizedMessage(string $code, string $fallback): string
    {
        return match ($code) {
            'VOUCHER_OUT_OF_STOCK', 'VOUCHER_NOT_FOUND' => 'ຊອງນີ້ຖືກໃຊ້ແລ້ວ ຫຼື ບໍ່ພົບຊອງ',
            'VOUCHER_EXPIRED' => 'ຊອງໝົດອາຍຸແລ້ວ',
            'VOUCHER_REACH_LIMIT' => 'ຊອງນີ້ຖືກຮັບຄົບຈຳນວນແລ້ວ',
            'TARGET_USER_NOT_FOUND', 'TARGET_USER_REDEEMED' => 'ຊອງນີ້ຖືກຮັບໄປແລ້ວ',
            'CANNOT_GET_OWN_VOUCHER' => 'ບໍ່ສາມາດຮັບຊອງຂອງຕົນເອງໄດ້',
            'INTERNAL_SERVER_ERROR' => 'ລະບົບ TrueMoney ຂັດຂ້ອງຊົ່ວຄາວ ກະລຸນາລອງໃໝ່',
            default => $fallback !== '' ? "ຮັບຊອງບໍ່ສຳເລັດ: {$fallback}" : 'ຮັບຊອງບໍ່ສຳເລັດ ກະລຸນາກວດສອບລິ້ງ',
        };
    }
}

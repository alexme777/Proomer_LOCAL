<?

/**
 * Class Sibirix_Model_Order_Row
 *
 */
class Sibirix_Model_Order_Row extends Sibirix_Model_Bitrix_Row {

    protected static $paySystemRef;

    /**
     *
     */

    public function includePaySystem() {
        $bxPaySysAction = new CSalePaySystemAction();

        $cSalePaymentAction = $bxPaySysAction->GetById(1);
        $paySysParams = unserialize($cSalePaymentAction['PARAMS']);

        $paySystemActionData = [
            "SHOULD_PAY" => $this->PRICE,
            "OrderDescr" => $this->ID,
            "CURRENCY" => $this->CURRENCY,
            "DATE_INSERT" => $this->DATE_INSERT,
            "EMAIL_USER" => $this->PROPS['EMAIL'],
        ];

        include(P_DR . $cSalePaymentAction["ACTION_FILE"] . "/payment.php");
        return $this;
    }
}
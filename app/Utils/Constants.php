<?php

namespace App\Utils;

class Constants
{
    const SETTING_ID = 1;

    const PAGE_HOME_ID = 1;

    const PAGE_ABOUT_ID = 2;

    const PAGE_BOOKING_ID = 3;

    const PAGE_PSYCHOLOGIST_ID = 4;

    const PAGE_SERVICE_ID = 5;

    const PAGE_EVENT_ID = 6;

    const PAGE_INSIGHT_ID = 7;

    const PAGE_CONTACT_ID = 8;

    const PAGE_TERMS_CONDITION_ID = 9;

    const PAGE_PRIVACY_POLICY_ID = 10;

    const PRODUCT_CONDITION_BNIB = "Brand New In Box";
    const PRODUCT_CONDITION_BNOB = "Brand New Open Box";
    const PRODUCT_CONDITION_VGOOD = "Very Good Condition";
    const PRODUCT_CONDITION_GOOD = "Good Condition";
    const PRODUCT_CONDITION_JUDGE = "Judge By Picture";
    const PRODUCT_CONDITIONS = [
        self::PRODUCT_CONDITION_BNIB,self::PRODUCT_CONDITION_BNOB,
        self::PRODUCT_CONDITION_VGOOD,self::PRODUCT_CONDITION_GOOD,
        self::PRODUCT_CONDITION_JUDGE];
    const PRODUCT_WARRANTY_ON = "On";
    const PRODUCT_WARRANTY_OFF = "Off";
    const PRODUCT_WARRANTIES = [self::PRODUCT_WARRANTY_ON,self::PRODUCT_WARRANTY_OFF];
    const PRODUCT_TYPE_CONSIGN = "Consign";
    const PRODUCT_TYPE_AUCTION = "Auction";
    const PRODUCT_TYPES = [self::PRODUCT_TYPE_CONSIGN,self::PRODUCT_TYPE_AUCTION];
    const PRODUCT_STATUS_WAITING_APPROVAL = 'Waiting Approval';
    const PRODUCT_STATUS_WAITING_CANCEL_APPROVAL = 'Waiting Cancel Approval';
    const PRODUCT_STATUS_APPROVED = 'Approved';
    const PRODUCT_STATUS_REJECTED = 'Rejected';
    const PRODUCT_STATUS_CANCELED = 'Canceled';
    const PRODUCT_STATUS_ACTIVE = 'Active';
    const PRODUCT_STATUS_SOLD = 'Sold';
    const PRODUCT_STATUS_CLOSED = 'Closed';

    const PARTNER_STATUS_WAITING_APPROVAL = 'Waiting Approval';
    const PARTNER_STATUS_APPROVED = 'Approved';
    const PARTNER_STATUS_REJECTED = 'Rejected';

    const ROLE_SUPER_ADMIN_ID = 1;
    const ROLE_SUPER_ADMIN_CODE = "super_admin";
    const ROLE_SUPER_ADMIN = "Super Admin";
    const ROLE_PARTNER_ID = 2;
    const ROLE_PARTNER_CODE= "partner";
    const ROLE_PARTNER = "Partner";
    const ROLE_PUBLIC_ID = 3;
    const ROLE_PUBLIC_CODE = 'public';
    const ROLE_PUBLIC = "Public";

    const XENDIT_FEE_VIRTUAL_ACCOUNT_AMOUNT = 4500;

    const XENDIT_FEE_CREDIT_CARD_AMOUNT = 2000;

    const XENDIT_FEE_CREDIT_CARD_PERCENTAGE = 2.9;

    const PERMISSION_RWD = ['read', 'write' ,'delete'];
    const PERMISSION_RW = ['read', 'write'];
    const PERMISSION_R = ['read'];
}

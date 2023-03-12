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

    // PRODUCT
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

    // PARTNER
    const PARTNER_STATUS_WAITING_APPROVAL = 'Waiting Approval';
    const PARTNER_STATUS_APPROVED = 'Approved';
    const PARTNER_STATUS_REJECTED = 'Rejected';

    // ROLE
    const ROLE_SUPER_ADMIN_ID = 1;
    const ROLE_SUPER_ADMIN_CODE = "super_admin";
    const ROLE_SUPER_ADMIN = "Super Admin";
    const ROLE_PARTNER_ID = 2;
    const ROLE_PARTNER_CODE= "partner";
    const ROLE_PARTNER = "Partner";
    const ROLE_PUBLIC_ID = 3;
    const ROLE_PUBLIC_CODE = 'public';
    const ROLE_PUBLIC = "Public";

    // ORDER
    const ORDER_STATUS_WAITING_PAYMENT = 'Waiting Payment';
    const ORDER_STATUS_EXPIRED = 'Expired';
    const ORDER_STATUS_PAID = 'Paid';
    const ORDER_STATUS_PROCESS = 'Process';
    const ORDER_STATUS_FINISH = 'Finish';
    const ORDER_STATUS_CANCELED = 'Canceled';
    const ORDER_PARTNER_STATUS_WAITING_CONFIRM = 'Waiting Confirm';
    const ORDER_PARTNER_STATUS_PROCESSING = 'Processing';
    const ORDER_PARTNER_STATUS_CANCELED = 'Canceled';
    const ORDER_PARTNER_STATUS_ON_DELIVERY = 'On Delivery';
    const ORDER_PARTNER_STATUS_COMPLETED = 'Completed';
    const ORDER_USER_STATUS_PAID = 'Paid';
    const ORDER_USER_STATUS_CANCELED = 'Canceled';
    const ORDER_USER_STATUS_PROCESSED = 'Processed';
    const ORDER_USER_STATUS_ON_DELIVERY = 'On Delivery';
    const ORDER_USER_STATUS_ON_COMPLAINED = 'Complained';
    const ORDER_USER_STATUS_COMPLETED = 'Completed';

    // INVOICE
    const PAYMENT_METHOD_TYPE_VIRTUAL_ACCOUNT = 'Virtual Account';
    const PAYMENT_METHOD_TYPE_CREDIT_CARD = 'Credit Card';
    const INVOICE_STATUS_PENDING = 'Pending';
    const INVOICE_STATUS_PAID = 'Paid';
    const INVOICE_STATUS_CANCELED = 'Canceled';
    const INVOICE_EXPIRES = 60;
    const INVOICE_FEE_PLATFORM_AMOUNT_PERCENTAGE = 10;

    // XENDIT
    const XENDIT_INVOICE_STATUS_PAID = 'PAID';
    const XENDIT_INVOICE_STATUS_EXPIRED = 'EXPIRED';
    const XENDIT_FEE_VIRTUAL_ACCOUNT_AMOUNT = 4500;
    const XENDIT_FEE_CREDIT_CARD_AMOUNT = 2000;
    const XENDIT_FEE_CREDIT_CARD_PERCENTAGE = 2.9;

    // PERMISSION
    const PERMISSION_RWD = ['read', 'write' ,'delete'];
    const PERMISSION_RW = ['read', 'write'];
    const PERMISSION_R = ['read'];
}

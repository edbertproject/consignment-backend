<?php

namespace App\Utils;

class Constants
{
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
    const PRODUCT_TYPE_SPECIAL_AUCTION = "Special Auction";
    const PRODUCT_TYPES = [self::PRODUCT_TYPE_CONSIGN,self::PRODUCT_TYPE_AUCTION, self::PRODUCT_TYPE_SPECIAL_AUCTION];
    const PRODUCT_STATUS_WAITING_APPROVAL = 'Waiting Approval';
    const PRODUCT_STATUS_WAITING_CANCEL_APPROVAL = 'Waiting Cancel Approval';
    const PRODUCT_STATUS_CANCEL_APPROVED = 'Cancel Approved';
    const PRODUCT_STATUS_APPROVED = 'Approved';
    const PRODUCT_STATUS_REJECTED = 'Rejected';
    const PRODUCT_STATUS_ACTIVE = 'Active';
    const PRODUCT_STATUS_SOLD = 'Sold';
    const PRODUCT_STATUS_CLOSED = 'Closed';
    const PRODUCT_STATUSES = [
        self::PRODUCT_STATUS_WAITING_APPROVAL,self::PRODUCT_STATUS_WAITING_CANCEL_APPROVAL,
        self::PRODUCT_STATUS_CANCEL_APPROVED,self::PRODUCT_STATUS_APPROVED,
        self::PRODUCT_STATUS_REJECTED,self::PRODUCT_STATUS_ACTIVE,
        self::PRODUCT_STATUS_SOLD,self::PRODUCT_STATUS_CLOSED
    ];

    // AUCTION
    const PRODUCT_AUCTION_EXPIRES = 12;

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
    const ORDER_STATUS_PROBLEM = 'Problem';
    const ORDER_STATUS_FINISH = 'Finish';
    const ORDER_STATUS_CANCELED = 'Canceled';

    const ORDER_SELLER_STATUS_WAITING_CONFIRM = 'Waiting Confirm';
    const ORDER_SELLER_STATUS_PROCESSING = 'Processing';
    const ORDER_SELLER_STATUS_CANCELED = 'Canceled';
    const ORDER_SELLER_STATUS_ON_DELIVERY = 'On Delivery';
    const ORDER_SELLER_STATUS_ARRIVED = 'Arrived';
    const ORDER_SELLER_STATUS_COMPLAIN = 'On Complain';
    const ORDER_SELLER_STATUS_COMPLETE = 'Complete';

    const ORDER_SELLER_STATUS_WAITING_CONFIRM_EXPIRE = 2 * 24; // in hour
    const ORDER_SELLER_STATUS_PROCESSING_EXPIRE = 2 * 24; // in hour

    const ORDER_BUYER_STATUS_PAID = 'Paid';
    const ORDER_BUYER_STATUS_CANCELED = 'Canceled';
    const ORDER_BUYER_STATUS_PROCESSED = 'Processed';
    const ORDER_BUYER_STATUS_ON_DELIVERY = 'On Delivery';
    const ORDER_BUYER_STATUS_ARRIVED = 'Arrived';
    const ORDER_BUYER_STATUS_COMPLAIN = 'Complained';
    const ORDER_BUYER_STATUS_COMPLETE = 'Complete';

    const ORDER_BUYER_STATUS_ARRIVED_EXPIRE = 24; // in hour

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

    // RAJA ONGKIR
    const RAJA_ONGKIR_COURIER_JNE = 'jne';
    const RAJA_ONGKIR_COURIER_POS = 'pos';
    const RAJA_ONGKIR_COURIER_TIKI = 'tiki';
    const RAJA_ONGKIR_COURIERS = [
        self::RAJA_ONGKIR_COURIER_JNE,
        self::RAJA_ONGKIR_COURIER_POS,
        self::RAJA_ONGKIR_COURIER_TIKI
    ];
    const RAJA_ONGKIR_DEFAULT_CITY_ID = 456;

    // PERMISSION
    const PERMISSION_RWD = ['read', 'write' ,'delete'];
    const PERMISSION_RW = ['read', 'write'];
    const PERMISSION_R = ['read'];
}

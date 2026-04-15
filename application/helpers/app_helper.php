<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function app_currency($amount)
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function app_date($date, $with_time = FALSE)
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);
    if (!$timestamp) {
        return '-';
    }

    return date($with_time ? 'd M Y H:i' : 'd M Y', $timestamp);
}

function invoice_status_badge($status)
{
    $map = array(
        'draft' => 'secondary',
        'sent' => 'primary',
        'partial' => 'warning',
        'paid' => 'success',
        'overdue' => 'danger',
        'cancelled' => 'dark',
        'approved' => 'success',
        'rejected' => 'danger',
        'expired' => 'dark',
    );

    return $map[$status] ?? 'secondary';
}

function document_number($prefix, $last_number)
{
    return sprintf('%s%s%04d', $prefix, date('Ym'), $last_number + 1);
}

function setting_value($settings, $key, $default = '')
{
    if (is_array($settings) && array_key_exists($key, $settings)) {
        return $settings[$key];
    }

    return $default;
}

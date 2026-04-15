<?php $uri = $this->uri->segment(1); ?>
<aside class="sidebar">
    <div class="brand-block">
        <div class="brand-mark">MP</div>
        <div>
            <div class="brand-name"><?= html_escape(setting_value($app_settings ?? array(), 'company_name', 'MoizPayment')); ?></div>
            <small><?= html_escape(setting_value($app_settings ?? array(), 'company_tagline', 'Invoice & Finance')); ?></small>
        </div>
    </div>

    <nav class="nav flex-column side-nav">
        <a class="nav-link <?= $uri === 'dashboard' || $uri === '' ? 'active' : ''; ?>" href="<?= site_url('dashboard'); ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a class="nav-link <?= $uri === 'clients' ? 'active' : ''; ?>" href="<?= site_url('clients'); ?>"><i class="bi bi-people"></i> Klien</a>
        <a class="nav-link <?= $uri === 'quotations' ? 'active' : ''; ?>" href="<?= site_url('quotations'); ?>"><i class="bi bi-file-earmark-text"></i> Penawaran</a>
        <a class="nav-link <?= $uri === 'invoices' ? 'active' : ''; ?>" href="<?= site_url('invoices'); ?>"><i class="bi bi-receipt"></i> Invoice</a>
        <a class="nav-link <?= $uri === 'finance' && $this->uri->segment(2) === 'income' ? 'active' : ''; ?>" href="<?= site_url('finance/income'); ?>"><i class="bi bi-cash-stack"></i> Uang Masuk</a>
        <a class="nav-link <?= $uri === 'finance' && $this->uri->segment(2) === 'expenses' ? 'active' : ''; ?>" href="<?= site_url('finance/expenses'); ?>"><i class="bi bi-wallet2"></i> Uang Keluar</a>
        <a class="nav-link <?= $uri === 'reports' ? 'active' : ''; ?>" href="<?= site_url('reports'); ?>"><i class="bi bi-bar-chart-line"></i> Laporan</a>
        <a class="nav-link <?= $uri === 'settings' ? 'active' : ''; ?>" href="<?= site_url('settings'); ?>"><i class="bi bi-gear"></i> Pengaturan</a>
    </nav>
</aside>

<main class="content-area">
    <header class="topbar">
        <div>
            <h1 class="page-title"><?= isset($page_title) ? html_escape($page_title) : 'MoizPayment'; ?></h1>
            <div class="page-subtitle">Sistem manajemen invoice, quotation, dan keuangan</div>
        </div>
        <div class="topbar-actions">
            <div class="user-pill">
                <strong><?= html_escape($current_user['full_name'] ?? 'Guest'); ?></strong>
                <span><?= html_escape($current_user['role_name'] ?? '-'); ?></span>
            </div>
            <a class="btn btn-outline-light btn-sm" href="<?= site_url('logout'); ?>">Logout</a>
        </div>
    </header>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= html_escape($this->session->flashdata('success')); ?></div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= html_escape($this->session->flashdata('error')); ?></div>
    <?php endif; ?>

    <?php if (validation_errors()): ?>
        <div class="alert alert-danger"><?= validation_errors(); ?></div>
    <?php endif; ?>

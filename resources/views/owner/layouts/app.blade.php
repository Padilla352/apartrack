<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>APARTRACK - @yield('title', 'Property Management')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Master CSS -->
    <link rel="stylesheet" href="{{ asset('css/master.css') }}">
    
    <style>
        /* ========== YOUR EXISTING STYLES ========== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #FFFFFF; color: #333333; }
        .top-header { background: #000333; border-bottom: 1px solid #E0E0E0; padding: 0 40px; display: flex; align-items: center; justify-content: space-between; height: 80px; position: sticky; top: 0; z-index: 100; }
        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo-icon { width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; background: rgba(255,255,255,0.1); box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        .logo-img { width: 100%; height: 100%; object-fit: cover; }
        .logo-text { font-size: 26px; font-weight: 700; color: #FFFFFF; letter-spacing: 1px; }
        .nav-tabs { display: flex; gap: 8px; background: rgba(255,255,255,0.1); padding: 4px; border-radius: 12px; }
        .nav-tab { padding: 10px 28px; border-radius: 12px; font-size: 15px; font-weight: 600; color: #FFFFFF; text-decoration: none; transition: all 0.2s; cursor: pointer; opacity: 0.8; }
        .nav-tab.active { background: #00A2FF; color: white; opacity: 1; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .nav-tab:hover:not(.active) { background: rgba(255,255,255,0.2); color: white; opacity: 1; }
        .action-icons { display: flex; align-items: center; gap: 24px; }
        .mail-icon { position: relative; cursor: pointer; color: #FFFFFF; transition: all 0.2s; text-decoration: none; display: flex; align-items: center; }
        .mail-icon i { font-size: 24px; color: #FFFFFF; transition: all 0.2s; }
        .mail-icon:hover i { color: #00A2FF; }
        .mail-badge { position: absolute; top: -6px; right: -10px; background: #D90404; color: white; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 20px; min-width: 18px; text-align: center; }
        .notification-icon { position: relative; cursor: pointer; }
        .notification-icon i { font-size: 24px; color: #FFFFFF; transition: all 0.2s; }
        .notification-icon:hover i { color: #FFEB3B; }
        .notification-badge { position: absolute; top: -6px; right: -10px; background: #D90404; color: white; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 20px; min-width: 18px; text-align: center; transition: all 0.2s; }
        .notification-badge.pulse { animation: badgePulse 0.5s ease; }
        @keyframes badgePulse { 0% { transform: scale(1); } 50% { transform: scale(1.3); } 100% { transform: scale(1); } }
        .user-dropdown { position: relative; display: inline-block; }
        .user-avatar { width: 48px; height: 48px; background: #00A2FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; position: relative; overflow: hidden; }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-avatar i { color: white; font-size: 24px; }
        .user-avatar:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(0,162,255,0.3); }
        .user-avatar::after { content: ''; position: absolute; top: 2px; right: 2px; width: 10px; height: 10px; background: #B4E662; border-radius: 50%; border: 2px solid #000333; display: none; }
        .user-avatar.has-notification::after { display: block; }
        .user-dropdown-menu { position: absolute; top: 55px; right: 0; width: 280px; background: white; border-radius: 16px; box-shadow: 0 12px 28px rgba(0,0,0,0.2); z-index: 1000; overflow: hidden; animation: dropdownFadeIn 0.2s ease; display: none; }
        .user-dropdown-menu.show { display: block; }
        @keyframes dropdownFadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-header { padding: 16px; background: linear-gradient(135deg, #000333 0%, #1a1a4e 100%); color: white; }
        .dropdown-user-info { display: flex; flex-direction: column; gap: 4px; }
        .dropdown-user-info strong { font-size: 14px; font-weight: 600; }
        .dropdown-user-info span { font-size: 11px; opacity: 0.8; }
        .dropdown-divider { height: 1px; background: #E0E0E0; margin: 8px 0; }
        .dropdown-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #333; text-decoration: none; transition: all 0.2s; cursor: pointer; }
        .dropdown-item:hover { background: #F0F2F5; }
        .dropdown-item i { width: 20px; font-size: 16px; color: #D90404; }
        .dropdown-item span { font-size: 14px; font-weight: 500; }
        .dropdown-item.settings-item i { color: #007BFF; }
        .notification-dropdown { position: absolute; top: 70px; right: 120px; width: 400px; max-width: calc(100vw - 20px); background: white; border-radius: 16px; box-shadow: 0 12px 28px rgba(0,0,0,0.2), 0 2px 4px rgba(0,0,0,0.05); z-index: 1000; overflow: hidden; animation: notificationFadeIn 0.2s ease; }
        @keyframes notificationFadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .notification-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #E0E0E0; background: white; }
        .notification-header h3 { font-size: 18px; font-weight: 700; color: #000333; margin: 0; }
        .mark-all-read { background: none; border: none; color: #007BFF; font-size: 12px; font-weight: 500; cursor: pointer; padding: 4px 8px; border-radius: 6px; transition: all 0.2s; }
        .mark-all-read:hover { background: #F0F0F0; }
        .notification-list { max-height: 400px; overflow-y: auto; }
        .notification-item { display: flex; padding: 14px 20px; border-bottom: 1px solid #F0F0F0; cursor: pointer; transition: background 0.2s; text-decoration: none; color: inherit; }
        .notification-item:hover { background: #F8F9FA; }
        .notification-item.unread { background: #E8F0FE; }
        .notification-item.unread:hover { background: #DCE6F5; }
        .notification-icon-dropdown { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; }
        .notification-icon-dropdown.created { background: #E8F5E9; color: #4CAF50; }
        .notification-icon-dropdown.updated { background: #FFF8E1; color: #FFC107; }
        .notification-icon-dropdown.deleted { background: #FFEBEE; color: #F44336; }
        .notification-icon-dropdown.default { background: #E3F2FD; color: #2196F3; }
        .notification-content { flex: 1; }
        .notification-title { font-size: 14px; font-weight: 600; color: #000333; margin-bottom: 4px; line-height: 1.4; }
        .notification-message { font-size: 12px; color: #666; margin-top: 4px; line-height: 1.4; }
        .notification-time { font-size: 11px; color: #888; margin-top: 4px; }
        .notification-empty { text-align: center; padding: 40px 20px; color: #888; }
        .notification-empty i { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
        .notification-footer { padding: 12px 20px; text-align: center; border-top: 1px solid #E0E0E0; background: white; }
        .see-all-link { color: #007BFF; text-decoration: none; font-size: 14px; font-weight: 500; }
        .see-all-link:hover { text-decoration: underline; }
        .main-content { padding: 28px 32px; max-width: 1400px; margin: 0 auto; }
        .page-header { margin-bottom: 28px; }
        .page-title { font-size: 28px; font-weight: 700; color: #000333; text-transform: lowercase; }
        /* Settings Modal */
        .modal-container-settings { background: white; border-radius: 24px; width: 500px; max-width: 95%; max-height: 85vh; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: modalSlideUp 0.3s ease-out; }
        .modal-close-settings { background: none; border: none; font-size: 20px; cursor: pointer; color: #999; padding: 8px; border-radius: 50%; transition: all 0.2s; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; }
        .modal-close-settings:hover { background: #F0F0F0; color: #333; }
        .settings-tabs { display: flex; border-bottom: 1px solid #E0E0E0; padding: 0 24px; background: white; }
        .settings-tab { background: none; border: none; padding: 16px 24px; font-size: 14px; font-weight: 500; color: #666; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid transparent; }
        .settings-tab:hover { color: #007BFF; }
        .settings-tab.active { color: #007BFF; border-bottom-color: #007BFF; }
        .settings-body { padding: 24px; max-height: calc(85vh - 120px); overflow-y: auto; }
        .settings-tab-content { display: none; }
        .settings-tab-content.active { display: block; animation: fadeIn 0.3s ease; }
        .settings-photo-section { display: flex; align-items: center; gap: 24px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #E0E0E0; }
        .settings-photo-preview { width: 80px; height: 80px; border-radius: 50%; overflow: hidden; background: #F0F0F0; border: 2px solid #007BFF; }
        .settings-photo-preview img { width: 100%; height: 100%; object-fit: cover; }
        .btn-upload-photo { background: #F0F0F0; color: #333; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .btn-upload-photo:hover { background: #007BFF; color: white; }
        .photo-hint { display: block; font-size: 11px; color: #999; margin-top: 8px; }
        .settings-form-group { margin-bottom: 20px; }
        .settings-form-group label { display: block; font-weight: 500; font-size: 13px; color: #333; margin-bottom: 8px; }
        .settings-input { width: 100%; padding: 12px 16px; border: 1px solid #E0E0E0; border-radius: 12px; font-size: 14px; transition: all 0.2s; }
        .settings-input:focus { outline: none; border-color: #007BFF; box-shadow: 0 0 0 2px rgba(0,123,255,0.1); }
        .password-input-wrapper { position: relative; }
        .toggle-password-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #999; padding: 4px; }
        .toggle-password-btn:hover { color: #007BFF; }
        .password-strength-meter { margin-top: 8px; }
        .strength-bar { height: 4px; background: #E0E0E0; border-radius: 2px; width: 100%; margin-bottom: 6px; transition: all 0.3s; }
        .strength-bar.weak { background: #D90404; width: 25%; }
        .strength-bar.fair { background: #FFC107; width: 50%; }
        .strength-bar.good { background: #00A2FF; width: 75%; }
        .strength-bar.strong { background: #B4E662; width: 100%; }
        .strength-text { font-size: 11px; color: #666; }
        .password-match-message { font-size: 11px; margin-top: 6px; }
        .match-success { color: #B4E662; }
        .match-error { color: #D90404; }
        .settings-form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 24px; border-top: 1px solid #E0E0E0; }
        .btn-cancel-settings { background: #F0F0F0; border: 1px solid #E0E0E0; padding: 10px 24px; border-radius: 10px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .btn-cancel-settings:hover { background: #E0E0E0; }
        .btn-save-settings { background: #007BFF; color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .btn-save-settings:hover { background: #0056b3; transform: translateY(-1px); }
        /* Property Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
        .modal-container { background: #FFFFFF; border-radius: 32px; width: 950px; max-width: 95%; max-height: 85vh; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: modalSlideUp 0.3s ease-out; }
        @keyframes modalSlideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 28px; border-bottom: 1px solid #E0E0E0; }
        .modal-back-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #333333; padding: 8px; border-radius: 50%; transition: all 0.2s; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
        .modal-back-btn:hover { background: #F0F0F0; }
        .modal-title { font-size: 20px; font-weight: 600; color: #000333; margin: 0; }
        .progress-steps { display: flex; align-items: center; padding: 16px 28px; background: #F0F0F0; border-bottom: 1px solid #E0E0E0; }
        .step { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .step-circle { width: 32px; height: 32px; background: #E0E0E0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: #666666; transition: all 0.2s; }
        .step.active .step-circle { background: #007BFF; color: white; }
        .step.completed .step-circle { background: #B4E662; color: #000333; }
        .step-label { font-size: 12px; font-weight: 500; color: #666666; }
        .step.active .step-label { color: #000333; font-weight: 600; }
        .step-line { flex: 1; height: 1px; background: #E0E0E0; margin: 0 12px; }
        .modal-body { padding: 28px 32px; max-height: calc(85vh - 200px); overflow-y: auto; }
        .step-content { display: none; animation: fadeIn 0.3s ease; }
        .step-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }
        .form-section { margin-bottom: 28px; }
        .section-title { font-size: 18px; font-weight: 600; color: #000333; margin-bottom: 8px; }
        .section-subtitle { font-size: 14px; color: #666666; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 500; font-size: 14px; color: #333333; margin-bottom: 8px; }
        .required { color: #D90404; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid #E0E0E0; border-radius: 12px; font-size: 14px; font-family: inherit; transition: all 0.2s; background: white; color: #111827; }
        .form-control:focus { outline: none; border-color: #007BFF; box-shadow: 0 0 0 2px rgba(0,123,255,0.1); background: white; }
        .form-control::placeholder { color: #9CA3AF; }
        .form-hint { display: block; font-size: 12px; color: #666666; margin-top: 6px; }
        .form-row { display: flex; gap: 16px; }
        .form-row .half { flex: 1; }
        
        textarea.form-control { background: white; color: #111827; resize: vertical; }
        .currency-input { position: relative; }
        .currency-symbol { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #666666; font-weight: 500; }
        .currency-input .with-currency { padding-left: 32px; }
        .radio-group { display: flex; gap: 24px; }
        .radio-label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: #333333; }
        .radio-label input[type="radio"] { width: 18px; height: 18px; cursor: pointer; accent-color: #007BFF; }
        .amenities-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 12px; }
        .amenity-checkbox { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #E0E0E0; border-radius: 12px; cursor: pointer; transition: all 0.2s; }
        .amenity-checkbox:hover { border-color: #007BFF; background: #F0F0F0; }
        .amenity-checkbox input { width: 18px; height: 18px; cursor: pointer; accent-color: #007BFF; }
        .amenity-name { font-size: 14px; color: #333333; flex: 1; }
        .multi-upload-area { border: 2px dashed #E0E0E0; border-radius: 16px; background: #F0F0F0; transition: all 0.2s; margin-bottom: 24px; }
        .multi-upload-area:hover { border-color: #007BFF; background: #FFFFFF; }
        .upload-trigger { padding: 40px; text-align: center; cursor: pointer; }
        .upload-icon { font-size: 48px; color: #007BFF; margin-bottom: 16px; }
        .upload-text { font-size: 14px; color: #333333; font-weight: 500; margin-bottom: 8px; }
        .upload-hint { font-size: 12px; color: #666666; margin-bottom: 12px; }
        .upload-count { font-size: 12px; color: #007BFF; font-weight: 500; }
        .image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px,1fr)); gap: 16px; margin-top: 16px; }
        .gallery-item { position: relative; border-radius: 12px; overflow: hidden; aspect-ratio:1/1; background: #F0F0F0; border: 1px solid #E0E0E0; transition: all 0.2s; }
        .gallery-item:hover { transform: scale(1.02); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
        .gallery-item .remove-btn { position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; background: rgba(0,0,0,0.6); border: none; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; backdrop-filter: blur(4px); }
        .gallery-item .remove-btn:hover { background: #D90404; transform: scale(1.05); }
        .main-image-badge { position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.7); color: #00A2FF; font-size: 10px; font-weight: 600; padding: 4px 8px; border-radius: 20px; backdrop-filter: blur(4px); }
        .set-main-btn { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); border: none; border-radius: 20px; color: white; font-size: 10px; padding: 4px 8px; cursor: pointer; transition: all 0.2s; backdrop-filter: blur(4px); }
        .set-main-btn:hover { background: #007BFF; }
        .modal-footer { padding: 20px 32px; border-top: 1px solid #E0E0E0; display: flex; justify-content: flex-end; gap: 16px; background: white; position: sticky; bottom: 0; z-index: 10; box-shadow: 0 -4px 10px rgba(0,0,0,0.05); }
        .btn-secondary { background: #F0F0F0; border: 1px solid #E0E0E0; padding: 14px 32px; border-radius: 12px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s; color: #333333; }
        .btn-secondary:hover { border-color: #D90404; background: #D90404; color: white; }
        .btn-primary-next { background: #B4E662; color: #000333; border: none; padding: 14px 36px; border-radius: 12px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s; }
        .btn-primary-next:hover { background: #00A2FF; color: white; transform: translateY(-1px); }
        .btn-submit { background: #B4E662; color: #000333; border: none; padding: 14px 36px; border-radius: 12px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s; }
        .btn-submit:hover { background: #00A2FF; color: white; transform: translateY(-1px); }
        .hidden { display: none; }
        .permit-input-wrapper { position: relative; }
        .permit-hint { font-size: 11px; color: #6c757d; margin-top: 5px; }
        .permit-validation { font-size: 11px; margin-top: 5px; color: #28a745; }
        .permit-validation i { margin-right: 4px; }
        .permit-validation.error { color: #D90404; }
        .permit-validation.error i { color: #D90404; }
        .permit-valid { border-color: #B4E662 !important; background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23B4E662"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>'); background-repeat: no-repeat; background-position: right 10px center; background-size: 20px; padding-right: 35px; }
        .permit-invalid { border-color: #D90404 !important; background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23D90404"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>'); background-repeat: no-repeat; background-position: right 10px center; background-size: 20px; padding-right: 35px; }
        .permit-counter { font-size: 10px; color: #6c757d; margin-top: 4px; text-align: right; }
        
        /* CUSTOM DROPDOWN STYLES - WHITE BACKGROUND */
        .custom-select-wrapper {
            position: relative;
            width: 100%;
        }
        .custom-select-trigger {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E0E0E0;
            border-radius: 12px;
            font-size: 14px;
            background: #FFFFFF !important;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
            color: #333333 !important;
        }
        .custom-select-trigger:hover {
            border-color: #007BFF;
        }
        .custom-select-trigger.open {
            border-color: #007BFF;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.1);
        }
        .custom-select-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #FFFFFF !important;
            border: 1px solid #E0E0E0;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            max-height: 250px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
            margin-top: 4px;
        }
        .custom-select-options.show {
            display: block;
        }
        .custom-select-option {
            padding: 12px 16px;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 14px;
            background: #FFFFFF !important;
            color: #333333 !important;
        }
        .custom-select-option:hover {
            background: #F0F2F5 !important;
        }
        .custom-select-option.selected {
            background: #E8F0FE !important;
            color: #007BFF !important;
            font-weight: 500;
        }
        .custom-select-arrow {
            transition: transform 0.2s;
            color: #666666;
        }
        .custom-select-arrow.open {
            transform: rotate(180deg);
        }
        .hidden-select {
            display: none;
        }
        
        @media (max-width: 900px) {
            .top-header { padding: 0 20px; height: 70px; }
            .logo-text { font-size: 20px; }
            .logo-icon { width: 38px; height: 38px; }
            .nav-tab { padding: 8px 20px; font-size: 13px; }
            .mail-icon i, .notification-icon i { font-size: 20px; }
            .user-avatar { width: 40px; height: 40px; }
            .user-avatar i { font-size: 18px; }
            .user-dropdown-menu { right: -10px; width: 260px; }
            .main-content { padding: 20px 16px; }
            .amenities-grid { grid-template-columns: 1fr; }
            .modal-container { width: 95%; max-height: 90vh; }
            .modal-body { padding: 20px 24px; max-height: calc(90vh - 180px); }
            .modal-footer { padding: 16px 24px; }
            .btn-secondary, .btn-primary-next, .btn-submit { padding: 12px 24px; font-size: 14px; }
            .notification-dropdown { right: 20px; top: 60px; width: 350px; }
        }
        @media (max-width: 600px) {
            .top-header { padding: 0 16px; height: 60px; }
            .logo-text { font-size: 16px; }
            .logo-icon { width: 32px; height: 32px; }
            .nav-tab { padding: 6px 14px; font-size: 11px; }
            .mail-icon i, .notification-icon i { font-size: 18px; }
            .user-avatar { width: 36px; height: 36px; }
            .user-avatar i { font-size: 16px; }
            .user-dropdown-menu { width: 240px; }
            .action-icons { gap: 12px; }
            .step-label { display: none; }
            .step-line { margin: 0 6px; }
            .form-row { flex-direction: column; }
            .upload-trigger { padding: 24px; }
            .modal-footer { flex-direction: column-reverse; }
            .btn-secondary, .btn-primary-next, .btn-submit { width: 100%; justify-content: center; }
            .notification-dropdown { right: 10px; top: 55px; width: 320px; }
        }
        
        /* ========== ADD LISTING BUTTONS STYLES ========== */
        .add-listing-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .btn-add-apartment {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #00A2FF, #007BFF);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-add-apartment:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 162, 255, 0.3);
        }
        
        .btn-add-business {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #B4E662, #00A2FF);
            color: #000333;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-add-business:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(180, 230, 98, 0.3);
        }
        
        .btn-add-business.disabled,
        .btn-add-business:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- TOP HEADER BAR -->
    <div class="top-header">
        <div class="logo-section">
            <div class="logo-icon"><img src="{{ asset('images/logo.png') }}" alt="APARTRACK Logo" class="logo-img"></div>
            <span class="logo-text">APARTrack</span>
        </div>
        <div class="nav-tabs">
            <a href="{{ route('owner.dashboard') }}" class="nav-tab {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('owner.apartments.index') }}" class="nav-tab {{ request()->routeIs('owner.apartments.*') ? 'active' : '' }}">My Listing</a>
        </div>
        <div class="action-icons">
            <a href="{{ route('owner.messages.index') }}" class="mail-icon"><i class="far fa-envelope"></i><span class="mail-badge" id="mailCount">0</span></a>
            <div class="notification-icon" id="notificationBell"><i class="far fa-bell"></i><span class="notification-badge" id="notificationCount">0</span></div>
            <div class="user-dropdown" id="userDropdown">
                <div class="user-avatar" id="userAvatarBtn">
                    @php $ownerUser = Auth::guard('owner')->user(); $avatarUrl = $ownerUser && $ownerUser->profile_photo_url ? asset($ownerUser->profile_photo_url) : 'https://ui-avatars.com/api/?background=00A2FF&color=fff&name='.urlencode($ownerUser->name ?? 'Owner'); @endphp
                    @if($ownerUser && $ownerUser->profile_photo_url) <img src="{{ $avatarUrl }}" alt="Avatar" style="width:48px;height:48px;border-radius:50%;object-fit:cover;"> @else <i class="fas fa-user-circle" style="font-size:48px;"></i> @endif
                </div>
                <div class="user-dropdown-menu" id="userDropdownMenu">
                    <div class="dropdown-header"><div class="dropdown-user-info"><strong>{{ Auth::guard('owner')->user()->name ?? 'Owner' }}</strong><span>{{ Auth::guard('owner')->user()->email ?? 'owner@apartrack.com' }}</span></div></div>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item settings-item" id="settingsBtn"><i class="fas fa-cog"></i><span>Settings</span></a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" id="logoutBtn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
                    <form id="logout-form" action="{{ route('owner.logout') }}" method="POST" style="display:none;">@csrf</form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Dropdown -->
    <div class="notification-dropdown" id="notificationDropdown" style="display:none;">
        <div class="notification-header">
            <h3>Notifications</h3>
            <button class="mark-all-read" id="markAllReadBtn">Mark all as read</button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="notification-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
        </div>
        <div class="notification-footer">
            <a href="#" class="see-all-link" id="seeAllNotifications">See All Notifications</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">@yield('page-title', 'dashboard')</h1>
        </div>
        
        @php
            $ownerUser = Auth::guard('owner')->user();
            $propertyType = $ownerUser->property_type ?? 'apartment';
            $isApproved = DB::table('permit_applications')
                ->where('user_id', $ownerUser->id)
                ->where('status', 'approved')
                ->exists();
            $canAddApartment = ($propertyType == 'apartment' || $propertyType == 'both') && $isApproved;
            $canAddBusiness = ($propertyType == 'both') && $isApproved;
        @endphp
        
        @yield('content')
    </div>

    <!-- Settings Modal -->
    <div id="settingsModal" class="modal-overlay hidden">
        <div class="modal-container-settings">
            <div class="modal-header"><h2 class="modal-title">Settings</h2><button type="button" class="modal-close-settings" id="closeSettingsModal"><i class="fas fa-times"></i></button></div>
            <div class="settings-tabs"><button class="settings-tab active" data-tab="profile"><i class="fas fa-user"></i> Profile</button><button class="settings-tab" data-tab="security"><i class="fas fa-lock"></i> Security</button></div>
            <div class="settings-body">
                <div class="settings-tab-content active" id="profileTab">
                    <form id="profileForm" enctype="multipart/form-data">@csrf
                        <div class="settings-photo-section"><div class="settings-photo-preview" id="photoPreview"><img src="{{ $currentPhoto ?? 'https://ui-avatars.com/api/?background=00A2FF&color=fff&name='.urlencode($ownerUser->name ?? 'Owner') }}" alt="Profile Photo" id="profilePhotoPreview" style="width:100%;height:100%;object-fit:cover;"></div><div class="settings-photo-upload"><label for="profile_photo" class="btn-upload-photo"><i class="fas fa-camera"></i> Change Photo</label><input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;"><small class="photo-hint">JPG, PNG or GIF. Max 2MB.</small></div></div>
                        <div class="settings-form-group"><label for="settings_name">Full Name</label><input type="text" id="settings_name" name="name" class="settings-input" value="{{ $ownerUser->name ?? '' }}" required></div>
                        <div class="settings-form-group"><label for="settings_email">Email Address</label><input type="email" id="settings_email" name="email" class="settings-input" value="{{ $ownerUser->email ?? '' }}" required></div>
                        <div class="settings-form-actions"><button type="button" class="btn-cancel-settings" id="cancelProfileBtn">Cancel</button><button type="submit" class="btn-save-settings">Save Changes</button></div>
                    </form>
                </div>
                <div class="settings-tab-content" id="securityTab">
                    <form id="securityForm">@csrf
                        <div class="settings-form-group"><label for="current_password">Current Password</label><div class="password-input-wrapper"><input type="password" id="current_password" name="current_password" class="settings-input" required><button type="button" class="toggle-password-btn" onclick="togglePasswordField('current_password')"><i class="fas fa-eye"></i></button></div></div>
                        <div class="settings-form-group"><label for="new_password">New Password</label><div class="password-input-wrapper"><input type="password" id="new_password" name="new_password" class="settings-input" required><button type="button" class="toggle-password-btn" onclick="togglePasswordField('new_password')"><i class="fas fa-eye"></i></button></div><div class="password-strength-meter"><div class="strength-bar"></div><span class="strength-text">Password strength: <span id="strengthLabel">None</span></span></div></div>
                        <div class="settings-form-group"><label for="confirm_password">Confirm New Password</label><div class="password-input-wrapper"><input type="password" id="confirm_password" name="confirm_password" class="settings-input" required><button type="button" class="toggle-password-btn" onclick="togglePasswordField('confirm_password')"><i class="fas fa-eye"></i></button></div><div id="passwordMatchMessage" class="password-match-message"></div></div>
                        <div class="settings-form-actions"><button type="button" class="btn-cancel-settings" id="cancelSecurityBtn">Cancel</button><button type="submit" class="btn-save-settings">Update Password</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- APARTMENT LISTING MODAL -->
    <div id="addListingModal" class="modal-overlay hidden">
        <div class="modal-container">
            <div class="modal-header"><button type="button" class="modal-back-btn" id="closeModalBtn"><i class="fas fa-arrow-left"></i></button><h2 class="modal-title">List your property</h2><div style="width:32px;"></div></div>
            <form action="{{ route('owner.apartments.store') }}" method="POST" enctype="multipart/form-data" id="addListingForm">
                @csrf
                <div class="progress-steps">
                    <div class="step active" data-step="1"><div class="step-circle">1</div><span class="step-label">Basics</span></div><div class="step-line"></div>
                    <div class="step" data-step="2"><div class="step-circle">2</div><span class="step-label">Location</span></div><div class="step-line"></div>
                    <div class="step" data-step="3"><div class="step-circle">3</div><span class="step-label">Amenities</span></div><div class="step-line"></div>
                    <div class="step" data-step="4"><div class="step-circle">4</div><span class="step-label">Photos</span></div>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Basics -->
                    <div class="step-content active" data-step-content="1">
                        <div class="form-section"><h3 class="section-title">Tell us about your place</h3><p class="section-subtitle">Guests love to know what makes your property special</p>
                            <div class="form-group"><label>Property title <span class="required">*</span></label><input type="text" name="name" class="form-control" placeholder="e.g., Cozy Studio in the City Center" required><small class="form-hint">Give your property a catchy name that stands out</small></div>
                            <div class="form-row"><div class="form-group half"><label>Unit number <span class="required">*</span></label><input type="text" name="unit_number" class="form-control" placeholder="e.g., A-101" required></div><div class="form-group half"><label>Property type <span class="required">*</span></label><select name="type" class="form-control" required><option value="">Select type</option><option value="Studio">Studio</option><option value="1BR">1 Bedroom</option><option value="2BR">2 Bedroom</option><option value="3BR">3 Bedroom</option><option value="Penthouse">Penthouse</option></select></div></div>
                            <div class="form-row"><div class="form-group half"><label>Bedrooms</label><input type="number" name="bedrooms" class="form-control" value="1" min="0" max="10"></div><div class="form-group half"><label>Bathrooms</label><input type="number" name="bathrooms" class="form-control" value="1" min="0" max="10"></div></div>
                            <div class="form-row"><div class="form-group half"><label>Monthly rent (₱) <span class="required">*</span></label><div class="currency-input"><span class="currency-symbol">₱</span><input type="number" name="monthly_rent" class="form-control with-currency" placeholder="0.00" required></div></div><div class="form-group half"><label>Floor area (sqm)</label><input type="number" name="floor_area_sqm" class="form-control" placeholder="e.g., 45"></div></div>
                            <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="4" placeholder="Describe what makes your place unique..."></textarea></div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Location -->
                    <div class="step-content" data-step-content="2">
                        <div class="form-section"><h3 class="section-title">Where is your place located?</h3><p class="section-subtitle">Guests need to know the area</p>
                            <div class="form-group" style="margin-top: 0;">
                                <label>Barangay <span class="required">*</span></label>
                                <div class="custom-select-wrapper" id="apartmentBarangayWrapper">
                                    <div class="custom-select-trigger" id="apartmentBarangayTrigger">
                                        <span id="apartmentSelectedValue">Select barangay</span>
                                        <i class="fas fa-chevron-down custom-select-arrow" id="apartmentArrow"></i>
                                    </div>
                                    <div class="custom-select-options" id="apartmentBarangayOptions">
                                        <div class="custom-select-option" data-value="Balangobong">Balangobong</div>
                                        <div class="custom-select-option" data-value="Bued">Bued</div>
                                        <div class="custom-select-option" data-value="Bugayong">Bugayong</div>
                                        <div class="custom-select-option" data-value="Camangaan">Camangaan</div>
                                        <div class="custom-select-option" data-value="Canarvacanan">Canarvacanan</div>
                                        <div class="custom-select-option" data-value="Capas">Capas</div>
                                        <div class="custom-select-option" data-value="Cili">Cili</div>
                                        <div class="custom-select-option" data-value="Dumayat">Dumayat</div>
                                        <div class="custom-select-option" data-value="Linmansangan">Linmansangan</div>
                                        <div class="custom-select-option" data-value="Mangcasuy">Mangcasuy</div>
                                        <div class="custom-select-option" data-value="Moreno">Moreno</div>
                                        <div class="custom-select-option" data-value="Pasileng Norte">Pasileng Norte</div>
                                        <div class="custom-select-option" data-value="Pasileng Sur">Pasileng Sur</div>
                                        <div class="custom-select-option" data-value="Poblacion">Poblacion</div>
                                        <div class="custom-select-option" data-value="San Felipe Central">San Felipe Central</div>
                                        <div class="custom-select-option" data-value="San Felipe Sur">San Felipe Sur</div>
                                        <div class="custom-select-option" data-value="San Pablo">San Pablo</div>
                                        <div class="custom-select-option" data-value="Santa Catalina">Santa Catalina</div>
                                        <div class="custom-select-option" data-value="Santa Maria Norte">Santa Maria Norte</div>
                                        <div class="custom-select-option" data-value="Santiago">Santiago</div>
                                        <div class="custom-select-option" data-value="Santo Niño">Santo Niño</div>
                                        <div class="custom-select-option" data-value="Sumabnit">Sumabnit</div>
                                        <div class="custom-select-option" data-value="Tabuyoc">Tabuyoc</div>
                                        <div class="custom-select-option" data-value="Vacante">Vacante</div>
                                    </div>
                                </div>
                                <input type="hidden" name="barangay_name" id="apartmentBarangayInput" required>
                            </div>
                            <div class="form-group"><label>Street address <span class="required">*</span></label><input type="text" name="address" class="form-control" placeholder="e.g., 123 Main Street" required></div>
                            <div class="form-group">
                                <label>Permit Number</label>
                                <div class="permit-input-wrapper">
                                    <input type="text" name="permit_number" id="permit_number" class="form-control" value="{{ Auth::guard('owner')->user()->permit_number ?? '' }}" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                    <div class="permit-hint"><i class="fas fa-info-circle"></i> This is your registered permit number - cannot be changed</div>
                                </div>
                            </div>
                            <div class="form-group"><label>Listing status</label><div class="radio-group"><label class="radio-label"><input type="radio" name="status" value="Vacant" checked> <span>Available now</span></label><label class="radio-label"><input type="radio" name="status" value="Reserved"> <span>Reserved</span></label></div></div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Amenities -->
                    <div class="step-content" data-step-content="3">
                        <div class="form-section"><h3 class="section-title">What amenities do you offer?</h3><p class="section-subtitle">Select all that apply</p>
                            <div class="amenities-grid">
                                @php $amenitiesList = ['Wifi','Kitchen','Parking','Air conditioning','Washer','Dryer','TV','Heating','Pool','Gym','Elevator','Security cameras','Smoke alarm','First aid kit','Fire extinguisher','Workspace']; @endphp
                                @foreach($amenitiesList as $amenity)<label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="{{ $amenity }}"><span class="amenity-name">{{ $amenity }}</span></label>@endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 4: Photos -->
                    <div class="step-content" data-step-content="4">
                        <div class="form-section"><h3 class="section-title">Add photos of your place</h3><p class="section-subtitle">Showcase what makes your property special. You can upload multiple photos.</p>
                            <div class="multi-upload-area" id="multiUploadArea"><input type="file" id="multipleImageUpload" name="images[]" class="hidden" accept="image/*" multiple onchange="previewMultipleImages(this)"><div class="upload-trigger" onclick="document.getElementById('multipleImageUpload').click()"><i class="fas fa-cloud-upload-alt upload-icon"></i><p class="upload-text">Drag and drop or click to upload</p><p class="upload-hint">You can select multiple images at once (PNG, JPG, up to 2MB each)</p><p class="upload-count" id="uploadCount">No files selected</p></div></div>
                            <div class="image-gallery" id="imageGallery"></div><input type="hidden" name="images_json" id="imagesJson" value="">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="price" value="0">
                <div class="modal-footer"><button type="button" class="btn-secondary" id="prevBtn" style="display:none;">Back</button><button type="button" class="btn-primary-next" id="nextBtn">Continue</button><button type="submit" class="btn-submit" id="submitBtn" style="display:none;">List your property</button></div>
            </form>
        </div>
    </div>

    <!-- BUSINESS LISTING MODAL -->
    @if($canAddBusiness)
    <div id="addBusinessModal" class="modal-overlay hidden">
        <div class="modal-container">
            <div class="modal-header">
                <button type="button" class="modal-back-btn" id="closeBusinessModalBtn"><i class="fas fa-arrow-left"></i></button>
                <h2 class="modal-title">List your Business Space</h2>
                <div style="width:32px;"></div>
            </div>
            <form action="{{ route('owner.business-spaces.store') }}" method="POST" enctype="multipart/form-data" id="addBusinessForm">
                @csrf
                <div class="progress-steps">
                    <div class="step active" data-step="1"><div class="step-circle">1</div><span class="step-label">Basics</span></div><div class="step-line"></div>
                    <div class="step" data-step="2"><div class="step-circle">2</div><span class="step-label">Location</span></div><div class="step-line"></div>
                    <div class="step" data-step="3"><div class="step-circle">3</div><span class="step-label">Amenities</span></div><div class="step-line"></div>
                    <div class="step" data-step="4"><div class="step-circle">4</div><span class="step-label">Photos</span></div>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Business Basics -->
                    <div class="step-content active" data-step-content="1">
                        <div class="form-section"><h3 class="section-title">Tell us about your business space</h3><p class="section-subtitle">Help customers discover your business</p>
                            <div class="form-group"><label>Business Name <span class="required">*</span></label><input type="text" name="business_name" class="form-control" placeholder="e.g., Starbucks Coffee" required><small class="form-hint">Your registered business name</small></div>
                            <div class="form-row"><div class="form-group half"><label>Unit number</label><input type="text" name="unit_number" class="form-control" placeholder="e.g., Unit 101"></div><div class="form-group half"><label>Business Type <span class="required">*</span></label><select name="type" class="form-control" required><option value="">Select type</option><option value="Office">Office Space</option><option value="Retail">Retail Store</option><option value="Restaurant">Restaurant / Cafe</option><option value="Warehouse">Warehouse</option><option value="Co-working">Co-working Space</option><option value="Studio">Studio</option><option value="Other">Other</option></select></div></div>
                            <div class="form-row"><div class="form-group half"><label>Monthly Rent (₱) <span class="required">*</span></label><div class="currency-input"><span class="currency-symbol">₱</span><input type="number" name="monthly_rent" class="form-control with-currency" placeholder="0.00" required></div></div><div class="form-group half"><label>Floor Area (sqm)</label><input type="number" name="floor_area_sqm" class="form-control" placeholder="e.g., 100"></div></div>
                            <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="4" placeholder="Describe your business, services offered, and what makes it unique..."></textarea></div>
                            <div class="form-group"><label>Listing status</label><div class="radio-group"><label class="radio-label"><input type="radio" name="status" value="Available" checked> <span>Available now</span></label><label class="radio-label"><input type="radio" name="status" value="Reserved"> <span>Reserved</span></label><label class="radio-label"><input type="radio" name="status" value="Occupied"> <span>Occupied</span></label></div></div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Location -->
                    <div class="step-content" data-step-content="2">
                        <div class="form-section"><h3 class="section-title">Where is your business located?</h3><p class="section-subtitle">Customers need to know the area</p>
                            <div class="form-group" style="margin-top: 0;">
                                <label>Barangay <span class="required">*</span></label>
                                <div class="custom-select-wrapper" id="businessBarangayWrapper">
                                    <div class="custom-select-trigger" id="businessBarangayTrigger">
                                        <span id="businessSelectedValue">Select barangay</span>
                                        <i class="fas fa-chevron-down custom-select-arrow" id="businessArrow"></i>
                                    </div>
                                    <div class="custom-select-options" id="businessBarangayOptions">
                                        <div class="custom-select-option" data-value="Balangobong">Balangobong</div>
                                        <div class="custom-select-option" data-value="Bued">Bued</div>
                                        <div class="custom-select-option" data-value="Bugayong">Bugayong</div>
                                        <div class="custom-select-option" data-value="Camangaan">Camangaan</div>
                                        <div class="custom-select-option" data-value="Canarvacanan">Canarvacanan</div>
                                        <div class="custom-select-option" data-value="Capas">Capas</div>
                                        <div class="custom-select-option" data-value="Cili">Cili</div>
                                        <div class="custom-select-option" data-value="Dumayat">Dumayat</div>
                                        <div class="custom-select-option" data-value="Linmansangan">Linmansangan</div>
                                        <div class="custom-select-option" data-value="Mangcasuy">Mangcasuy</div>
                                        <div class="custom-select-option" data-value="Moreno">Moreno</div>
                                        <div class="custom-select-option" data-value="Pasileng Norte">Pasileng Norte</div>
                                        <div class="custom-select-option" data-value="Pasileng Sur">Pasileng Sur</div>
                                        <div class="custom-select-option" data-value="Poblacion">Poblacion</div>
                                        <div class="custom-select-option" data-value="San Felipe Central">San Felipe Central</div>
                                        <div class="custom-select-option" data-value="San Felipe Sur">San Felipe Sur</div>
                                        <div class="custom-select-option" data-value="San Pablo">San Pablo</div>
                                        <div class="custom-select-option" data-value="Santa Catalina">Santa Catalina</div>
                                        <div class="custom-select-option" data-value="Santa Maria Norte">Santa Maria Norte</div>
                                        <div class="custom-select-option" data-value="Santiago">Santiago</div>
                                        <div class="custom-select-option" data-value="Santo Niño">Santo Niño</div>
                                        <div class="custom-select-option" data-value="Sumabnit">Sumabnit</div>
                                        <div class="custom-select-option" data-value="Tabuyoc">Tabuyoc</div>
                                        <div class="custom-select-option" data-value="Vacante">Vacante</div>
                                    </div>
                                </div>
                                <input type="hidden" name="barangay_name" id="businessBarangayInput" required>
                            </div>
                            <div class="form-group"><label>Street address <span class="required">*</span></label><input type="text" name="address" class="form-control" placeholder="e.g., 123 Main Street" required></div>
                            <div class="form-group"><label>Permit Number</label><div class="permit-input-wrapper"><input type="text" name="permit_number" id="business_permit_number" class="form-control" value="{{ Auth::guard('owner')->user()->permit_number ?? '' }}" readonly style="background-color: #e9ecef; cursor: not-allowed;"><div class="permit-hint"><i class="fas fa-info-circle"></i> This is your registered permit number - cannot be changed</div></div></div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Amenities & Features -->
                    <div class="step-content" data-step-content="3">
                        <div class="form-section"><h3 class="section-title">Amenities & Features</h3><p class="section-subtitle">Select all that apply</p>
                            <div class="form-group"><label>Amenities</label><div class="amenities-grid"><label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="Parking"> <span class="amenity-name">Parking</span></label><label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="24/7 Security"> <span class="amenity-name">24/7 Security</span></label><label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="CCTV"> <span class="amenity-name">CCTV</span></label><label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="Backup Power"> <span class="amenity-name">Backup Power</span></label><label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="Elevator"> <span class="amenity-name">Elevator</span></label><label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="Air Conditioning"> <span class="amenity-name">Air Conditioning</span></label><label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="Wifi"> <span class="amenity-name">Wifi</span></label><label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="Conference Room"> <span class="amenity-name">Conference Room</span></label></div></div>
                            <div class="form-group"><label>Business Features</label><div class="amenities-grid"><label class="amenity-checkbox"><input type="checkbox" name="business_features[]" value="Loading Bay"> <span class="amenity-name">Loading Bay</span></label><label class="amenity-checkbox"><input type="checkbox" name="business_features[]" value="Delivery Access"> <span class="amenity-name">Delivery Access</span></label><label class="amenity-checkbox"><input type="checkbox" name="business_features[]" value="Signage Space"> <span class="amenity-name">Signage Space</span></label><label class="amenity-checkbox"><input type="checkbox" name="business_features[]" value="Street Frontage"> <span class="amenity-name">Street Frontage</span></label><label class="amenity-checkbox"><input type="checkbox" name="business_features[]" value="Corner Lot"> <span class="amenity-name">Corner Lot</span></label><label class="amenity-checkbox"><input type="checkbox" name="business_features[]" value="Drive-thru Capable"> <span class="amenity-name">Drive-thru Capable</span></label></div></div>
                        </div>
                    </div>
                    
                    <!-- Step 4: Photos -->
                    <div class="step-content" data-step-content="4">
                        <div class="form-section">
                            <h3 class="section-title">Add photos of your business</h3>
                            <p class="section-subtitle">Showcase your business space. You can upload multiple photos.</p>
                            <div class="multi-upload-area" id="businessMultiUploadArea"><input type="file" id="businessMultipleImageUpload" name="images[]" class="hidden" accept="image/*" multiple onchange="previewBusinessImages(this)"><div class="upload-trigger" onclick="document.getElementById('businessMultipleImageUpload').click()"><i class="fas fa-cloud-upload-alt upload-icon"></i><p class="upload-text">Drag and drop or click to upload</p><p class="upload-hint">You can select multiple images at once (PNG, JPG, up to 2MB each)</p><p class="upload-count" id="businessUploadCount">No files selected</p></div></div>
                            <div class="image-gallery" id="businessImageGallery"></div><input type="hidden" name="images_json" id="businessImagesJson" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn-secondary" id="businessPrevBtn" style="display:none;">Back</button><button type="button" class="btn-primary-next" id="businessNextBtn">Continue</button><button type="submit" class="btn-submit" id="businessSubmitBtn" style="display:none;">List Business Space</button></div>
            </form>
        </div>
    </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global toggle password
        window.togglePasswordField = function(fieldId) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            const btn = field.parentElement.querySelector('.toggle-password-btn i');
            if (field.type === 'password') { field.type = 'text'; if(btn) btn.classList.remove('fa-eye'); if(btn) btn.classList.add('fa-eye-slash'); }
            else { field.type = 'password'; if(btn) btn.classList.remove('fa-eye-slash'); if(btn) btn.classList.add('fa-eye'); }
        };
        
        // Custom Dropdown Functions
        function initCustomDropdown(wrapperId, triggerId, optionsId, selectedSpanId, inputId, arrowId) {
            const wrapper = document.getElementById(wrapperId);
            const trigger = document.getElementById(triggerId);
            const optionsDiv = document.getElementById(optionsId);
            const selectedSpan = document.getElementById(selectedSpanId);
            const hiddenInput = document.getElementById(inputId);
            const arrow = document.getElementById(arrowId);
            
            if (!wrapper || !trigger) return;
            
            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    optionsDiv.classList.remove('show');
                    if (arrow) arrow.classList.remove('open');
                    if (trigger) trigger.classList.remove('open');
                }
            });
            
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                optionsDiv.classList.toggle('show');
                if (arrow) arrow.classList.toggle('open');
                trigger.classList.toggle('open');
            });
            
            const options = optionsDiv.querySelectorAll('.custom-select-option');
            options.forEach(option => {
                option.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;
                    selectedSpan.textContent = text;
                    hiddenInput.value = value;
                    
                    options.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    optionsDiv.classList.remove('show');
                    if (arrow) arrow.classList.remove('open');
                    if (trigger) trigger.classList.remove('open');
                });
            });
        }
        
        // Apartment Modal Functions
        let uploadedImages = [], mainImageIndex = 0;
        
        function previewMultipleImages(input) {
            const gallery = document.getElementById('imageGallery'), uploadCount = document.getElementById('uploadCount');
            if(input.files) {
                const newFiles = Array.from(input.files);
                newFiles.forEach(file => {
                    if(file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => { 
                            uploadedImages.push({ file: file, dataUrl: e.target.result, isMain: uploadedImages.length===0 }); 
                            if(uploadedImages.length===1) mainImageIndex=0; 
                            renderGallery(); 
                        };
                        reader.readAsDataURL(file);
                    }
                });
                if(uploadCount) uploadCount.textContent = `${uploadedImages.length} file(s) selected`;
            }
        }
        
        function renderGallery() {
            const gallery = document.getElementById('imageGallery'), uploadCount = document.getElementById('uploadCount');
            if(!gallery) return;
            if(uploadedImages.length===0) { gallery.innerHTML=''; if(uploadCount) uploadCount.textContent='No files selected'; return; }
            if(uploadCount) uploadCount.textContent = `${uploadedImages.length} file(s) selected`;
            gallery.innerHTML = uploadedImages.map((img,idx)=>`<div class="gallery-item" data-index="${idx}"><img src="${img.dataUrl}" alt="Property image ${idx+1}"><button type="button" class="remove-btn" onclick="removeImage(${idx})"><i class="fas fa-times"></i></button>${img.isMain?'<div class="main-image-badge"><i class="fas fa-star"></i> Main</div>':''}${!img.isMain?`<button type="button" class="set-main-btn" onclick="setMainImage(${idx})">Set as main</button>`:''}</div>`).join('');
        }
        
        function removeImage(index) { 
            uploadedImages.splice(index,1); 
            if(uploadedImages.length>0 && index===mainImageIndex) { uploadedImages[0].isMain=true; mainImageIndex=0; } 
            else if(uploadedImages.length===0) mainImageIndex=-1; 
            else if(index<mainImageIndex) mainImageIndex--; 
            uploadedImages.forEach((img,idx)=>{ img.isMain=(idx===mainImageIndex); }); 
            renderGallery(); 
            updateImagesJson(); 
        }
        
        function setMainImage(index) { 
            uploadedImages.forEach(img=>img.isMain=false); 
            uploadedImages[index].isMain=true; 
            mainImageIndex=index; 
            renderGallery(); 
            updateImagesJson(); 
        }
        
        function updateImagesJson() { 
            const input=document.getElementById('imagesJson'); 
            if(input) input.value=JSON.stringify(uploadedImages.map(img=>({ dataUrl:img.dataUrl, isMain:img.isMain, fileName:img.file?.name||'image.jpg' }))); 
        }
        
        // Business Modal Functions
        let businessUploadedImages = [], businessMainImageIndex = 0;
        
        function previewBusinessImages(input) {
            const gallery = document.getElementById('businessImageGallery'), uploadCount = document.getElementById('businessUploadCount');
            if(input.files) {
                const newFiles = Array.from(input.files);
                newFiles.forEach(file => {
                    if(file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => { 
                            businessUploadedImages.push({ file: file, dataUrl: e.target.result, isMain: businessUploadedImages.length===0 }); 
                            if(businessUploadedImages.length===1) businessMainImageIndex=0; 
                            renderBusinessGallery(); 
                        };
                        reader.readAsDataURL(file);
                    }
                });
                if(uploadCount) uploadCount.textContent = `${businessUploadedImages.length} file(s) selected`;
            }
        }
        
        function renderBusinessGallery() {
            const gallery = document.getElementById('businessImageGallery'), uploadCount = document.getElementById('businessUploadCount');
            if(!gallery) return;
            if(businessUploadedImages.length===0) { gallery.innerHTML=''; if(uploadCount) uploadCount.textContent='No files selected'; return; }
            if(uploadCount) uploadCount.textContent = `${businessUploadedImages.length} file(s) selected`;
            gallery.innerHTML = businessUploadedImages.map((img,idx)=>`<div class="gallery-item" data-index="${idx}"><img src="${img.dataUrl}" alt="Business image ${idx+1}"><button type="button" class="remove-btn" onclick="removeBusinessImage(${idx})"><i class="fas fa-times"></i></button>${img.isMain?'<div class="main-image-badge"><i class="fas fa-star"></i> Main</div>':''}${!img.isMain?`<button type="button" class="set-main-btn" onclick="setBusinessMainImage(${idx})">Set as main</button>`:''}</div>`).join('');
        }
        
        function removeBusinessImage(index) { 
            businessUploadedImages.splice(index,1); 
            if(businessUploadedImages.length>0 && index===businessMainImageIndex) { businessUploadedImages[0].isMain=true; businessMainImageIndex=0; } 
            else if(businessUploadedImages.length===0) businessMainImageIndex=-1; 
            else if(index<businessMainImageIndex) businessMainImageIndex--; 
            businessUploadedImages.forEach((img,idx)=>{ img.isMain=(idx===businessMainImageIndex); }); 
            renderBusinessGallery(); 
            updateBusinessImagesJson(); 
        }
        
        function setBusinessMainImage(index) { 
            businessUploadedImages.forEach(img=>img.isMain=false); 
            businessUploadedImages[index].isMain=true; 
            businessMainImageIndex=index; 
            renderBusinessGallery(); 
            updateBusinessImagesJson(); 
        }
        
        function updateBusinessImagesJson() { 
            const input=document.getElementById('businessImagesJson'); 
            if(input) input.value=JSON.stringify(businessUploadedImages.map(img=>({ dataUrl:img.dataUrl, isMain:img.isMain, fileName:img.file?.name||'image.jpg' }))); 
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize custom dropdowns
            initCustomDropdown('apartmentBarangayWrapper', 'apartmentBarangayTrigger', 'apartmentBarangayOptions', 'apartmentSelectedValue', 'apartmentBarangayInput', 'apartmentArrow');
            initCustomDropdown('businessBarangayWrapper', 'businessBarangayTrigger', 'businessBarangayOptions', 'businessSelectedValue', 'businessBarangayInput', 'businessArrow');
            
            // Settings Modal Logic
            const settingsModal = document.getElementById('settingsModal');
            const settingsBtn = document.getElementById('settingsBtn');
            const closeSettingsModal = document.getElementById('closeSettingsModal');
            const cancelProfileBtn = document.getElementById('cancelProfileBtn');
            const cancelSecurityBtn = document.getElementById('cancelSecurityBtn');
            const settingsTabs = document.querySelectorAll('.settings-tab');
            const settingsTabContents = document.querySelectorAll('.settings-tab-content');

            if (settingsBtn) {
                settingsBtn.addEventListener('click', function(e) { e.preventDefault(); if(settingsModal) { settingsModal.classList.remove('hidden'); document.body.style.overflow='hidden'; } });
            }
            function closeSettingsModalFunc() { if(settingsModal) { settingsModal.classList.add('hidden'); document.body.style.overflow=''; } }
            if(closeSettingsModal) closeSettingsModal.addEventListener('click', closeSettingsModalFunc);
            if(cancelProfileBtn) cancelProfileBtn.addEventListener('click', closeSettingsModalFunc);
            if(cancelSecurityBtn) cancelSecurityBtn.addEventListener('click', closeSettingsModalFunc);
            if(settingsModal) settingsModal.addEventListener('click', function(e) { if(e.target===settingsModal) closeSettingsModalFunc(); });
            settingsTabs.forEach(tab => { tab.addEventListener('click', function() { const tabId=this.dataset.tab; settingsTabs.forEach(t=>t.classList.remove('active')); this.classList.add('active'); settingsTabContents.forEach(c=>c.classList.remove('active')); document.getElementById(tabId+'Tab').classList.add('active'); }); });
            
            const profilePhotoInput=document.getElementById('profile_photo'), profilePhotoPreview=document.getElementById('profilePhotoPreview');
            if(profilePhotoInput) profilePhotoInput.addEventListener('change', function(e) { const file=e.target.files[0]; if(file) { const reader=new FileReader(); reader.onload=event=>{ if(profilePhotoPreview) profilePhotoPreview.src=event.target.result; }; reader.readAsDataURL(file); } });
            
            const newPass=document.getElementById('new_password'), confirmPass=document.getElementById('confirm_password'), strengthBar=document.querySelector('.strength-bar'), strengthLabel=document.getElementById('strengthLabel');
            function checkStrength(pwd){ let s=0; if(pwd.length>=8)s++; if(pwd.length>=12)s++; if(/[a-z]/.test(pwd))s++; if(/[A-Z]/.test(pwd))s++; if(/[0-9]/.test(pwd))s++; if(/[$@#&!%*?]/.test(pwd))s++; if(s<=2) return {text:'Weak',class:'weak'}; if(s<=4) return {text:'Fair',class:'fair'}; if(s<=5) return {text:'Good',class:'good'}; return {text:'Strong',class:'strong'}; }
            if(newPass) newPass.addEventListener('input',function(){ const st=checkStrength(this.value); if(strengthLabel) strengthLabel.textContent=st.text; if(strengthBar) strengthBar.className='strength-bar '+st.class; checkMatch(); });
            function checkMatch(){ const np=newPass?newPass.value:'', cp=confirmPass?confirmPass.value:'', msg=document.getElementById('passwordMatchMessage'); if(cp.length>0){ if(np===cp){ if(msg){ msg.innerHTML='<i class="fas fa-check-circle"></i> Passwords match'; msg.className='password-match-message match-success'; } } else { if(msg){ msg.innerHTML='<i class="fas fa-exclamation-circle"></i> Passwords do not match'; msg.className='password-match-message match-error'; } } } else if(msg) msg.innerHTML=''; }
            if(confirmPass) confirmPass.addEventListener('input',checkMatch);
            
            const profileForm=document.getElementById('profileForm');
            if(profileForm) profileForm.addEventListener('submit', async function(e){ e.preventDefault(); const fd=new FormData(this), btn=this.querySelector('.btn-save-settings'), orig=btn.innerHTML; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Saving...'; btn.disabled=true; try{ const resp=await fetch('/owner/update-profile',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},body:fd}); const txt=await resp.text(); let res; try{ res=JSON.parse(txt); }catch(e){ throw new Error('Server returned invalid response'); } if(res.success){ Swal.fire({title:'Success!',text:res.message,icon:'success',confirmButtonColor:'#B4E662'}).then(()=>location.reload()); } else throw new Error(res.message||'Something went wrong'); }catch(err){ Swal.fire({title:'Error',text:err.message,icon:'error',confirmButtonColor:'#D90404'}); }finally{ btn.innerHTML=orig; btn.disabled=false; } });
            
            const securityForm=document.getElementById('securityForm');
            if(securityForm) securityForm.addEventListener('submit', async function(e){ e.preventDefault(); const np=document.getElementById('new_password').value, cp=document.getElementById('confirm_password').value; if(np!==cp){ Swal.fire({title:'Error',text:'Passwords do not match!',icon:'error',confirmButtonColor:'#D90404'}); return; } if(np.length<8){ Swal.fire({title:'Error',text:'Password must be at least 8 characters!',icon:'error',confirmButtonColor:'#D90404'}); return; } const fd=new FormData(this), btn=this.querySelector('.btn-save-settings'), orig=btn.innerHTML; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Updating...'; btn.disabled=true; try{ const resp=await fetch('{{ route("owner.update.password") }}',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},body:fd}); const res=await resp.json(); if(res.success){ Swal.fire({title:'Success!',text:res.message,icon:'success',confirmButtonColor:'#B4E662'}).then(()=>{ document.getElementById('current_password').value=''; document.getElementById('new_password').value=''; document.getElementById('confirm_password').value=''; closeSettingsModalFunc(); }); } else throw new Error(res.message||'Something went wrong'); }catch(err){ Swal.fire({title:'Error',text:err.message,icon:'error',confirmButtonColor:'#D90404'}); }finally{ btn.innerHTML=orig; btn.disabled=false; } });
            
            // Apartment Modal
            const modal = document.getElementById('addListingModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            let currentStep = 1;
            const totalSteps = 4;
            
            function updateSteps() {
                const steps = document.querySelectorAll('#addListingModal .step');
                const contents = document.querySelectorAll('#addListingModal .step-content');
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                const submitBtn = document.getElementById('submitBtn');
                
                steps.forEach((step, idx) => {
                    const stepNum = idx + 1;
                    if (stepNum < currentStep) { step.classList.add('completed'); step.classList.remove('active'); }
                    else if (stepNum === currentStep) { step.classList.add('active'); step.classList.remove('completed'); }
                    else { step.classList.remove('active', 'completed'); }
                });
                
                contents.forEach((content, idx) => { if (idx + 1 === currentStep) content.classList.add('active'); else content.classList.remove('active'); });
                
                if (currentStep === 1) { if (prevBtn) prevBtn.style.display = 'none'; } else { if (prevBtn) prevBtn.style.display = 'block'; }
                if (currentStep === totalSteps) { if (nextBtn) nextBtn.style.display = 'none'; if (submitBtn) submitBtn.style.display = 'block'; } else { if (nextBtn) nextBtn.style.display = 'block'; if (submitBtn) submitBtn.style.display = 'none'; }
            }
            
            function openModal() { if(modal) { modal.classList.remove('hidden'); document.body.style.overflow='hidden'; currentStep=1; updateSteps(); uploadedImages=[]; mainImageIndex=0; renderGallery(); } }
            function closeModal() { if(modal) { modal.classList.add('hidden'); document.body.style.overflow=''; const form=document.getElementById('addListingForm'); if(form) form.reset(); uploadedImages=[]; mainImageIndex=0; renderGallery(); } }
            
            document.querySelectorAll('.open-property-modal, #showAddModalBtn').forEach(btn => btn.addEventListener('click', openModal));
            if(closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
            if(modal) modal.addEventListener('click', e => { if(e.target===modal) closeModal(); });
            
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');
            if(nextBtn) nextBtn.addEventListener('click', () => { if(currentStep < totalSteps) { currentStep++; updateSteps(); document.querySelector('#addListingForm .modal-body').scrollTop = 0; } });
            if(prevBtn) prevBtn.addEventListener('click', () => { if(currentStep > 1) { currentStep--; updateSteps(); document.querySelector('#addListingForm .modal-body').scrollTop = 0; } });
            document.querySelectorAll('#addListingModal .step').forEach((step,idx) => step.addEventListener('click', () => { if(idx+1 <= currentStep) { currentStep = idx+1; updateSteps(); } }));
            
            const addListingForm = document.getElementById('addListingForm');
            if (addListingForm) {
                addListingForm.addEventListener('submit', function(e) {
                    const barangayValue = document.getElementById('apartmentBarangayInput').value;
                    if (barangayValue === '') {
                        e.preventDefault();
                        Swal.fire({ title: 'Barangay required', text: 'Please select a barangay', icon: 'warning', confirmButtonColor: '#007BFF' });
                        return;
                    }
                    if (uploadedImages.length === 0) { e.preventDefault(); Swal.fire({ title: 'No photos uploaded', text: 'Please add at least one photo of your property', icon: 'warning', confirmButtonColor: '#007BFF' }); }
                });
            }
            
            // Business Modal
            const businessModal = document.getElementById('addBusinessModal');
            const closeBusinessModalBtn = document.getElementById('closeBusinessModalBtn');
            
            if (businessModal) {
                let businessCurrentStep = 1;
                const businessTotalSteps = 4;
                
                function updateBusinessSteps() {
                    const steps = document.querySelectorAll('#addBusinessModal .step');
                    const contents = document.querySelectorAll('#addBusinessModal .step-content');
                    const prevBtn = document.getElementById('businessPrevBtn');
                    const nextBtn = document.getElementById('businessNextBtn');
                    const submitBtn = document.getElementById('businessSubmitBtn');
                    
                    steps.forEach((step, idx) => { const stepNum = idx + 1; if (stepNum < businessCurrentStep) { step.classList.add('completed'); step.classList.remove('active'); } else if (stepNum === businessCurrentStep) { step.classList.add('active'); step.classList.remove('completed'); } else { step.classList.remove('active', 'completed'); } });
                    contents.forEach((content, idx) => { if (idx + 1 === businessCurrentStep) content.classList.add('active'); else content.classList.remove('active'); });
                    if (businessCurrentStep === 1) { if (prevBtn) prevBtn.style.display = 'none'; } else { if (prevBtn) prevBtn.style.display = 'block'; }
                    if (businessCurrentStep === businessTotalSteps) { if (nextBtn) nextBtn.style.display = 'none'; if (submitBtn) submitBtn.style.display = 'block'; } else { if (nextBtn) nextBtn.style.display = 'block'; if (submitBtn) submitBtn.style.display = 'none'; }
                }
                
                function openBusinessModal() { if(businessModal) { businessModal.classList.remove('hidden'); document.body.style.overflow='hidden'; businessCurrentStep=1; updateBusinessSteps(); businessUploadedImages=[]; businessMainImageIndex=0; renderBusinessGallery(); } }
                function closeBusinessModal() { if(businessModal) { businessModal.classList.add('hidden'); document.body.style.overflow=''; const form=document.getElementById('addBusinessForm'); if(form) form.reset(); businessUploadedImages=[]; businessMainImageIndex=0; renderBusinessGallery(); } }
                
                if(closeBusinessModalBtn) closeBusinessModalBtn.addEventListener('click', closeBusinessModal);
                if(businessModal) businessModal.addEventListener('click', e => { if(e.target===businessModal) closeBusinessModal(); });
                
                const businessNextBtn = document.getElementById('businessNextBtn');
                const businessPrevBtn = document.getElementById('businessPrevBtn');
                if(businessNextBtn) businessNextBtn.addEventListener('click', () => { if(businessCurrentStep < businessTotalSteps) { businessCurrentStep++; updateBusinessSteps(); document.querySelector('#addBusinessForm .modal-body').scrollTop = 0; } });
                if(businessPrevBtn) businessPrevBtn.addEventListener('click', () => { if(businessCurrentStep > 1) { businessCurrentStep--; updateBusinessSteps(); document.querySelector('#addBusinessForm .modal-body').scrollTop = 0; } });
                document.querySelectorAll('#addBusinessModal .step').forEach((step,idx) => step.addEventListener('click', () => { if(idx+1 <= businessCurrentStep) { businessCurrentStep = idx+1; updateBusinessSteps(); } }));
                
                const addBusinessForm = document.getElementById('addBusinessForm');
                if (addBusinessForm) {
                    addBusinessForm.addEventListener('submit', function(e) {
                        const barangayValue = document.getElementById('businessBarangayInput').value;
                        if (barangayValue === '') {
                            e.preventDefault();
                            Swal.fire({ title: 'Barangay required', text: 'Please select a barangay', icon: 'warning', confirmButtonColor: '#007BFF' });
                            return;
                        }
                        if (businessUploadedImages.length === 0) { e.preventDefault(); Swal.fire({ title: 'No photos uploaded', text: 'Please add at least one photo of your business space', icon: 'warning', confirmButtonColor: '#007BFF' }); }
                    });
                }
                
                window.openBusinessModal = openBusinessModal;
                window.closeBusinessModal = closeBusinessModal;
            }
            
            const showBusinessModalBtn = document.getElementById('showBusinessModalBtn');
            if (showBusinessModalBtn && typeof window.openBusinessModal === 'function') { showBusinessModalBtn.addEventListener('click', function(e) { e.preventDefault(); window.openBusinessModal(); }); }
            
            // ==================== REAL NOTIFICATION FUNCTIONS ====================
            let notificationDropdownOpen = false;
            
            function loadNotifications() {
                const notificationList = document.getElementById('notificationList');
                if(!notificationList) return;
                
                // FIXED: Use direct URL instead of route() to avoid route not found error
                fetch('/owner/notifications')
                    .then(response => response.json())
                    .then(data => {
                        notificationList.innerHTML = '';
                        if(data.notifications && data.notifications.length > 0) {
                            let unreadCount = 0;
                            data.notifications.forEach(notif => {
                                if(!notif.is_read) unreadCount++;
                                
                                let iconClass = 'default';
                                let iconName = 'fa-bell';
                                
                                switch(notif.type) {
                                    case 'message':
                                        iconClass = 'created';
                                        iconName = 'fa-envelope';
                                        break;
                                    case 'tenant':
                                        iconClass = 'updated';
                                        iconName = 'fa-user-plus';
                                        break;
                                    case 'apartment':
                                        iconClass = 'created';
                                        iconName = 'fa-building';
                                        break;
                                    case 'business':
                                        iconClass = 'created';
                                        iconName = 'fa-store';
                                        break;
                                    case 'payment':
                                        iconClass = 'updated';
                                        iconName = 'fa-money-bill';
                                        break;
                                    case 'maintenance':
                                        iconClass = 'deleted';
                                        iconName = 'fa-tools';
                                        break;
                                    case 'complaint':
                                        iconClass = 'deleted';
                                        iconName = 'fa-exclamation-triangle';
                                        break;
                                    case 'verification':
                                        iconClass = 'created';
                                        iconName = 'fa-check-circle';
                                        break;
                                    default:
                                        iconClass = 'default';
                                        iconName = 'fa-bell';
                                }
                                
                                notificationList.innerHTML += `
                                    <div class="notification-item ${!notif.is_read ? 'unread' : ''}" data-id="${notif.id}">
                                        <div class="notification-icon-dropdown ${iconClass}">
                                            <i class="fas ${iconName}"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">${escapeHtml(notif.title)}</div>
                                            <div class="notification-message">${escapeHtml(notif.message)}</div>
                                            <div class="notification-time">${notif.time_ago}</div>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            document.getElementById('notificationCount').textContent = unreadCount;
                            if(unreadCount > 0) {
                                document.getElementById('notificationCount').classList.add('pulse');
                                setTimeout(() => document.getElementById('notificationCount').classList.remove('pulse'), 500);
                            }
                            
                            document.querySelectorAll('.notification-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    const id = this.dataset.id;
                                    markNotificationAsRead(id);
                                    this.classList.remove('unread');
                                });
                            });
                        } else {
                            notificationList.innerHTML = `
                                <div class="notification-empty">
                                    <i class="far fa-bell-slash"></i>
                                    <p>No notifications yet</p>
                                </div>
                            `;
                            document.getElementById('notificationCount').textContent = '0';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        notificationList.innerHTML = `
                            <div class="notification-empty">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Unable to load notifications</p>
                            </div>
                        `;
                    });
            }
            
            function markNotificationAsRead(id) {
                fetch(`/owner/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }).catch(error => console.error('Error marking notification as read:', error));
            }
            
            function markAllAsRead() {
                // FIXED: Use direct URL instead of route()
                fetch('/owner/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    loadNotifications();
                    Swal.fire({
                        title: 'Marked as read',
                        text: 'All notifications have been marked as read',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }).catch(error => console.error('Error marking all as read:', error));
            }
            
            function toggleNotificationDropdown() {
                const dd = document.getElementById('notificationDropdown');
                if(!dd) return;
                if(notificationDropdownOpen) {
                    dd.style.display = 'none';
                    notificationDropdownOpen = false;
                } else {
                    loadNotifications();
                    dd.style.display = 'block';
                    notificationDropdownOpen = true;
                }
            }
            
            function closeNotificationDropdown() {
                const dd = document.getElementById('notificationDropdown');
                if(dd) {
                    dd.style.display = 'none';
                    notificationDropdownOpen = false;
                }
            }
            
            function loadNotificationCount() {
                // FIXED: Use direct URL instead of route()
                fetch('/owner/notifications/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('notificationCount').textContent = data.count || 0;
                    })
                    .catch(error => console.error('Error loading notification count:', error));
            }
            
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            // ==================== MAIL COUNT FUNCTION ====================
            function loadMailCount() { 
                fetch('{{ route("owner.messages.unread-count") }}')
                    .then(response => response.json())
                    .then(data => {
                        $('#mailCount').text(data.count || 0);
                    })
                    .catch(() => $('#mailCount').text('0'));
            }
            
            // ==================== USER DROPDOWN ====================
            const userAvatarBtn = document.getElementById('userAvatarBtn');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            
            function toggleUserDropdown() { 
                if(userDropdownMenu) userDropdownMenu.classList.toggle('show'); 
            }
            
            function closeUserDropdown() { 
                if(userDropdownMenu) userDropdownMenu.classList.remove('show'); 
            }
            
            function updateUserAvatarBadge() { 
                const ua = document.getElementById('userAvatarBtn'); 
                const cnt = parseInt($('#notificationCount').text()); 
                if(ua) { 
                    if(cnt > 0) ua.classList.add('has-notification'); 
                    else ua.classList.remove('has-notification'); 
                } 
            }
            
            if(userAvatarBtn) {
                userAvatarBtn.addEventListener('click', e => { 
                    e.stopPropagation(); 
                    toggleUserDropdown(); 
                });
            }
            
            document.addEventListener('click', function(e) { 
                const ud = document.getElementById('userDropdown'); 
                if(ud && !ud.contains(e.target)) closeUserDropdown(); 
            });
            
            // ==================== LOGOUT ====================
            const logoutBtn = document.getElementById('logoutBtn');
            if(logoutBtn) {
                logoutBtn.addEventListener('click', function(e) { 
                    e.preventDefault(); 
                    Swal.fire({ 
                        title: 'Logout', 
                        text: 'Are you sure?', 
                        icon: 'question', 
                        showCancelButton: true, 
                        confirmButtonColor: '#D90404', 
                        cancelButtonColor: '#333333', 
                        confirmButtonText: 'Yes, logout', 
                        cancelButtonText: 'Cancel' 
                    }).then(result => { 
                        if(result.isConfirmed) document.getElementById('logout-form').submit(); 
                    }); 
                    closeUserDropdown(); 
                });
            }
            
            // ==================== NOTIFICATION EVENT LISTENERS ====================
            document.getElementById('notificationBell')?.addEventListener('click', function(e) { 
                e.stopPropagation(); 
                toggleNotificationDropdown(); 
            });
            
            document.getElementById('markAllReadBtn')?.addEventListener('click', function(e) { 
                e.stopPropagation(); 
                markAllAsRead(); 
            });
            
            document.getElementById('seeAllNotifications')?.addEventListener('click', function(e) { 
                e.preventDefault(); 
                e.stopPropagation(); 
                closeNotificationDropdown();
                Swal.fire({
                    title: 'All Notifications',
                    html: '<div class="text-center">You can view all notifications in the notifications page</div>',
                    icon: 'info',
                    confirmButtonColor: '#007BFF'
                });
            });
            
            document.addEventListener('click', function(e) { 
                const bell = document.getElementById('notificationBell'); 
                const dd = document.getElementById('notificationDropdown'); 
                if(notificationDropdownOpen && dd && bell && !dd.contains(e.target) && !bell.contains(e.target)) { 
                    closeNotificationDropdown(); 
                } 
            });
            
            // ==================== INITIAL LOADS ====================
            loadNotificationCount(); 
            updateUserAvatarBadge(); 
            loadMailCount();
            
            // Poll for updates every 30 seconds
            setInterval(() => { 
                loadNotificationCount(); 
                loadMailCount(); 
                updateUserAvatarBadge(); 
            }, 30000);
            
            // ==================== CONFIRM DELETE ====================
            window.confirmDelete = function(formId) { 
                Swal.fire({ 
                    title: 'Are you sure?', 
                    text: "You won't be able to revert this!", 
                    icon: 'warning', 
                    showCancelButton: true, 
                    confirmButtonColor: '#D90404', 
                    cancelButtonColor: '#333333', 
                    confirmButtonText: 'Yes, delete it!' 
                }).then(result => { 
                    if(result.isConfirmed) document.getElementById(formId).submit(); 
                }); 
            };
            
            setTimeout(() => { $('.alert').fadeOut('slow'); }, 5000);
        });
    </script>
    @stack('scripts')
</body>
</html>
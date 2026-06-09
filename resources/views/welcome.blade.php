<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel - Welcome</title>
    
    <!-- Fonts: Instrument Sans (same as original) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <style>
        /* ---------- RESET & BASE ---------- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #FDFDFC;
            color: #1b1b18;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            transition: background-color 0.2s, color 0.2s;
        }

        /* Dark mode styles (respects system preference) */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0a0a0a;
                color: #EDEDEC;
            }
            .card-content {
                background-color: #161615;
                box-shadow: inset 0px 0px 0px 1px rgba(255, 250, 237, 0.18);
            }
            .graphic-panel {
                background-color: #1D0002;
            }
            .list-bullet-dot {
                background-color: #3E3E3A;
            }
            .list-connector::before {
                border-color: #3E3E3A;
            }
            .step-bullet {
                background-color: #161615;
                border-color: #3E3E3A;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            }
            .step-bullet-inner {
                background-color: #3E3E3A;
            }
            .text-secondary {
                color: #A1A09A;
            }
            .nav-link {
                border-color: transparent;
                color: #EDEDEC;
            }
            .nav-link-ghost:hover {
                border-color: #3E3E3A;
            }
            .nav-link-solid {
                border-color: #3E3E3A;
                color: #EDEDEC;
            }
            .nav-link-solid:hover {
                border-color: #62605b;
            }
            .btn-primary {
                background-color: #eeeeec;
                border-color: #eeeeec;
                color: #1C1C1A;
            }
            .btn-primary:hover {
                background-color: #ffffff;
                border-color: #ffffff;
            }
            .link-highlight {
                color: #FF4433;
            }
            .graphic-light {
                display: none;
            }
            .graphic-dark {
                display: block;
            }
        }

        /* Light mode defaults (also shown when no dark pref) */
        .graphic-light {
            display: block;
        }
        .graphic-dark {
            display: none;
        }

        /* Layout containers */
        .welcome-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            transition: opacity 0.75s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            opacity: 1;
        }

        /* entrance animation (replaces Tailwind starting styles) */
        .fade-up {
            animation: fadeSlideUp 0.75s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
        }

        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(24px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-card {
            display: flex;
            flex-direction: column-reverse;
            width: 100%;
            max-width: 335px;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        @media (min-width: 1024px) {
            body {
                padding: 2rem;
            }
            .main-card {
                flex-direction: row;
                max-width: 896px; /* lg:max-w-4xl ~ 896px */
            }
        }

        /* Content panel (left side) */
        .content-panel {
            flex: 1;
            background-color: #ffffff;
            padding: 1.5rem 1.5rem 3rem 1.5rem;
            box-shadow: inset 0px 0px 0px 1px rgba(26, 26, 0, 0.16);
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        @media (prefers-color-scheme: dark) {
            .content-panel {
                background-color: #161615;
                box-shadow: inset 0px 0px 0px 1px rgba(255, 250, 237, 0.18);
            }
        }

        @media (min-width: 1024px) {
            .content-panel {
                padding: 2rem 2rem 2rem 2rem;
                border-top-left-radius: 0.5rem;
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 0.5rem;
            }
        }

        /* Graphic panel (right side, photo area) */
        .graphic-panel {
            background-color: #fff2f2;
            position: relative;
            width: 100%;
            aspect-ratio: 335 / 376;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            overflow: hidden;
            flex-shrink: 0;
        }

        @media (min-width: 1024px) {
            .graphic-panel {
                width: 438px;
                aspect-ratio: auto;
                border-top-left-radius: 0;
                border-top-right-radius: 0.5rem;
                border-bottom-right-radius: 0.5rem;
                margin-left: -1px;
                margin-bottom: 0;
            }
        }

        .graphic-inner {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
        }

        .svg-stack {
            position: relative;
            width: 448px;
            max-width: none;
            margin-top: -4.9rem;
            margin-left: -2rem;
        }

        @media (min-width: 1024px) {
            .svg-stack {
                margin-top: -6.6rem;
                margin-left: 0;
            }
        }

        /* Overlay inner shadow on graphic panel */
        .graphic-overlay {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            box-shadow: inset 0px 0px 0px 1px rgba(26, 26, 0, 0.16);
            pointer-events: none;
        }
        @media (prefers-color-scheme: dark) {
            .graphic-overlay {
                box-shadow: inset 0px 0px 0px 1px rgba(255, 250, 237, 0.18);
            }
        }

        /* Typography */
        .title {
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        .description {
            font-size: 0.8125rem;
            line-height: 1.4;
            color: #706f6c;
            margin-bottom: 0.5rem;
        }
        @media (prefers-color-scheme: dark) {
            .description {
                color: #A1A09A;
            }
        }

        /* Custom list style (timeline / bullet with connector) */
        .resources-list {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
        }
        .list-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 0;
            position: relative;
        }
        /* vertical connector line (like before:border-l) */
        .list-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 0.4rem;
            top: 50%;
            bottom: 0;
            width: 0;
            border-left: 1px solid #e3e3e0;
            height: calc(100% - 0.5rem);
            transform: translateY(0.25rem);
            pointer-events: none;
        }
        .list-item:first-child::before {
            top: 1.2rem;
            height: calc(100% - 1rem);
        }
        @media (prefers-color-scheme: dark) {
            .list-item:not(:last-child)::before {
                border-left-color: #3E3E3A;
            }
        }
        /* bullet circle wrapper */
        .bullet-wrapper {
            position: relative;
            background-color: inherit;
            padding: 0.25rem 0;
            z-index: 2;
        }
        .bullet-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.125rem;
            height: 1.125rem;
            background-color: #FDFDFC;
            border-radius: 9999px;
            border: 1px solid #e3e3e0;
            box-shadow: 0px 0px 1px 0px rgba(0,0,0,0.03), 0px 1px 2px 0px rgba(0,0,0,0.06);
        }
        @media (prefers-color-scheme: dark) {
            .bullet-circle {
                background-color: #161615;
                border-color: #3E3E3A;
                box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            }
        }
        .bullet-dot {
            width: 0.5rem;
            height: 0.5rem;
            background-color: #dbdbd7;
            border-radius: 9999px;
        }
        @media (prefers-color-scheme: dark) {
            .bullet-dot {
                background-color: #3E3E3A;
            }
        }
        .list-link {
            font-size: 0.8125rem;
            line-height: 1.5;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-weight: 500;
            text-decoration: underline;
            text-underline-offset: 4px;
            color: #f53003;
        }
        @media (prefers-color-scheme: dark) {
            .list-link {
                color: #FF4433;
            }
        }
        .list-link svg {
            width: 0.625rem;
            height: 0.625rem;
        }
        .link-text {
            text-decoration: underline;
            text-underline-offset: 4px;
        }
        /* Action button */
        .action-button {
            display: inline-block;
            background-color: #1b1b18;
            border: 1px solid #000000;
            color: white;
            padding: 0.375rem 1.25rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }
        .action-button:hover {
            background-color: black;
            border-color: black;
        }
        /* header / navigation */
        .app-header {
            width: 100%;
            max-width: 335px;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 1024px) {
            .app-header {
                max-width: 896px;
            }
        }
        .nav-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
        }
        .nav-link {
            display: inline-block;
            padding: 0.375rem 1.25rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            line-height: 1.4;
            text-decoration: none;
            transition: border-color 0.2s;
            border: 1px solid transparent;
        }
        .nav-link-solid {
            border-color: #19140035;
            color: #1b1b18;
        }
        .nav-link-solid:hover {
            border-color: #1915014a;
        }
        @media (prefers-color-scheme: dark) {
            .nav-link-solid {
                border-color: #3E3E3A;
                color: #EDEDEC;
            }
            .nav-link-solid:hover {
                border-color: #62605b;
            }
        }
        .footer-spacer {
            height: 3.5rem;
            display: none;
        }
        @media (min-width: 1024px) {
            .footer-spacer {
                display: block;
            }
        }
        /* logo inside graphic area (top Laravel logo) */
        .laravel-logo-svg {
            width: 100%;
            max-width: 100%;
            color: #F53003;
            transition: transform 0.3s;
        }
        @media (prefers-color-scheme: dark) {
            .laravel-logo-svg {
                color: #F61500;
            }
        }
        .graphic-svg-group {
            transition: opacity 0.75s ease, transform 0.75s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        .animate-svg {
            animation: fadeSlideUp 0.75s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
        }
        /* adjust spacing for first list item line */
        .list-item:first-child .bullet-wrapper {
            padding-top: 0;
        }
    </style>
</head>
<body>

    <!-- Header with auth links (preserving Laravel blade logic if needed) -->
    <header class="app-header">
        <nav class="nav-bar">
                            <a href="#" class="nav-link nav-link-ghost">Log in</a>
                <a href="#" class="nav-link nav-link-solid">Register</a>
                    </nav>
    </header>

    <!-- Main content wrapper with animation -->
    <div class="welcome-wrapper fade-up">
        <main class="main-card">
            <!-- Left content area (text & actions) -->
            <div class="content-panel">
                <h1 class="title">Let's get started</h1>
                <p class="description">
                    Laravel has an incredibly rich ecosystem. <br>We suggest starting with the following.
                </p>

                <!-- List with custom bullet and connector lines -->
                <ul class="resources-list">
                    <li class="list-item">
                        <div class="bullet-wrapper">
                            <div class="bullet-circle">
                                <div class="bullet-dot"></div>
                            </div>
                        </div>
                        <span>
                            Read the
                            <a href="https://laravel.com/docs" target="_blank" class="list-link">
                                <span>Documentation</span>
                                <svg viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                                </svg>
                            </a>
                        </span>
                    </li>
                    <li class="list-item">
                        <div class="bullet-wrapper">
                            <div class="bullet-circle">
                                <div class="bullet-dot"></div>
                            </div>
                        </div>
                        <span>
                            Watch video tutorials at
                            <a href="https://laracasts.com" target="_blank" class="list-link">
                                <span>Laracasts</span>
                                <svg viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                                </svg>
                            </a>
                        </span>
                    </li>
                </ul>

                <div>
                    <a href="https://cloud.laravel.com" target="_blank" class="action-button">
                        Deploy now
                    </a>
                </div>
            </div>

            <!-- Right graphic panel (Laravel art + layered SVGs) -->
            <div class="graphic-panel">
                <div class="graphic-inner">
                    <!-- Laravel typography logo (top) -->
                    <svg class="laravel-logo-svg" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; top: 0; left: 0; z-index: 10; width: 100%; padding: 1rem 1rem 0 1rem;">
                        <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor" />
                        <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor" />
                        <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337ZM241.31 75.7472C240.312 78.4531 238.916 80.8078 237.122 82.8112C235.326 84.8161 233.156 86.4183 230.614 87.6206C228.07 88.823 225.251 89.4238 222.16 89.4238C219.068 89.4238 216.275 88.823 213.782 87.6206C211.289 86.4183 209.145 84.8161 207.35 82.8112C205.554 80.8078 204.182 78.4531 203.236 75.7472C202.286 73.0421 201.814 70.1868 201.814 67.1821C201.814 64.1758 202.286 61.3205 203.236 58.6154C204.182 55.9102 205.554 53.5571 207.35 51.5522C209.145 49.5495 211.288 47.9451 213.782 46.7427C216.275 45.5404 219.068 44.9396 222.16 44.9396C225.251 44.9396 228.07 45.5404 230.614 46.7427C233.156 47.9451 235.326 49.5487 237.122 51.5522C238.917 53.5571 240.312 55.9102 241.31 58.6154C242.306 61.3205 242.806 64.1758 242.806 67.1821C242.805 70.1868 242.305 73.0421 241.31 75.7472Z" fill="currentColor" />
                        <path d="M438 -3H421.694V102.197H438V-3Z" fill="currentColor" />
                        <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="currentColor" />
                        <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="currentColor" />
                        <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656ZM357.02 61.0967C357.145 59.7487 359.023 43.3761 376.442 43.3761C393.861 43.3761 395.978 59.7464 396.099 61.0967H357.02Z" fill="currentColor" />
                    </svg>

                    <!-- LAYERED SVG GRAPHICS (Light & Dark) with fade-up animation -->
                    <div class="svg-stack graphic-light animate-svg">
                        <!-- Light mode SVG (identical path to original with colors and blend modes) -->
                        <svg viewBox="0 0 440 376" fill="none" xmlns="http://www.w3.org/2000/svg" width="100%" height="auto">
                            <g>
                                <path d="M188.263 355.73L188.595 355.73C195.441 348.845 205.766 339.761 219.569 328.477C232.93 317.193 242.978 308.205 249.714 301.511C256.34 294.626 260.867 287.358 263.296 279.708C265.725 272.058 264.565 264.121 259.816 255.896C254.516 246.716 247.062 239.352 237.454 233.805C227.957 228.067 217.908 225.198 207.307 225.198C196.927 225.197 190.136 227.97 186.934 233.516C183.621 238.872 184.726 246.331 190.247 255.894L125.647 255.891C116.371 239.825 112.395 225.481 113.72 212.858C115.265 200.235 121.559 190.481 132.602 183.596C143.754 176.52 158.607 172.982 177.159 172.983C196.594 172.984 215.863 176.523 234.968 183.6C253.961 190.486 271.299 200.241 286.98 212.864C302.661 225.488 315.14 239.833 324.416 255.899C333.03 270.817 336.841 283.918 335.847 295.203C335.075 306.487 331.376 316.336 324.75 324.751C318.346 333.167 308.408 343.494 294.936 355.734L377.094 355.737L405.917 405.656L217.087 405.649L188.263 355.73Z" fill="black" stroke="#1B1B18" stroke-width="1"/>
                                <path d="M9.11884 226.339L-13.7396 226.338L-42.7286 176.132L43.0733 176.135L175.595 405.649L112.651 405.647L9.11884 226.339Z" fill="black" stroke="#1B1B18" stroke-width="1"/>
                                <path d="M204.592 327.449L204.923 327.449C211.769 320.564 222.094 311.479 235.897 300.196C249.258 288.912 259.306 279.923 266.042 273.23C272.668 266.345 277.195 259.077 279.624 251.427C282.053 243.777 280.893 235.839 276.145 227.615C270.844 218.435 263.39 211.071 253.782 205.524C244.285 199.786 234.236 196.917 223.635 196.916C213.255 196.916 206.464 199.689 203.262 205.235C199.949 210.59 201.054 218.049 206.575 227.612L141.975 227.61C132.699 211.544 128.723 197.2 130.048 184.577C131.593 171.954 137.887 162.2 148.93 155.315C160.083 148.239 174.935 144.701 193.487 144.702C212.922 144.703 232.192 148.242 251.296 155.319C270.289 162.205 287.627 171.96 303.308 184.583C318.989 197.207 331.468 211.552 340.745 227.618C349.358 242.536 353.169 255.637 352.175 266.921C351.403 278.205 347.704 288.055 341.078 296.47C334.674 304.885 324.736 315.213 311.264 327.453L393.422 327.456L422.246 377.375L233.415 377.368L204.592 327.449Z" fill="#F8B803" stroke="#1B1B18" stroke-width="1"/>
                                <path d="M25.447 198.058L2.58852 198.057L-26.4005 147.851L59.4015 147.854L191.923 377.368L128.979 377.365L25.447 198.058Z" fill="#F8B803" stroke="#1B1B18" stroke-width="1"/>
                            </g>
                        </svg>
                    </div>
                    <div class="svg-stack graphic-dark animate-svg">
                        <!-- Dark mode SVG variant (same as original dark-friendly) -->
                        <svg viewBox="0 0 440 376" fill="none" xmlns="http://www.w3.org/2000/svg" width="100%" height="auto">
                            <g>
                                <path d="M188.263 355.73L188.595 355.73C195.441 348.845 205.766 339.761 219.569 328.477C232.93 317.193 242.978 308.205 249.714 301.511C256.34 294.626 260.867 287.358 263.296 279.708C265.725 272.058 264.565 264.121 259.816 255.896C254.516 246.716 247.062 239.352 237.454 233.805C227.957 228.067 217.908 225.198 207.307 225.198C196.927 225.197 190.136 227.97 186.934 233.516C183.621 238.872 184.726 246.331 190.247 255.894L125.647 255.891C116.371 239.825 112.395 225.481 113.72 212.858C115.265 200.235 121.559 190.481 132.602 183.596C143.754 176.52 158.607 172.982 177.159 172.983C196.594 172.984 215.863 176.523 234.968 183.6C253.961 190.486 271.299 200.241 286.98 212.864C302.661 225.488 315.14 239.833 324.416 255.899C333.03 270.817 336.841 283.918 335.847 295.203C335.075 306.487 331.376 316.336 324.75 324.751C318.346 333.167 308.408 343.494 294.936 355.734L377.094 355.737L405.917 405.656L217.087 405.649L188.263 355.73Z" fill="black" stroke="#FF750F" stroke-width="1"/>
                                <path d="M9.11884 226.339L-13.7396 226.338L-42.7286 176.132L43.0733 176.135L175.595 405.649L112.651 405.647L9.11884 226.339Z" fill="black" stroke="#FF750F" stroke-width="1"/>
                                <path d="M204.592 327.449L204.923 327.449C211.769 320.564 222.094 311.479 235.897 300.196C249.258 288.912 259.306 279.923 266.042 273.23C272.668 266.345 277.195 259.077 279.624 251.427C282.053 243.777 280.893 235.839 276.145 227.615C270.844 218.435 263.39 211.071 253.782 205.524C244.285 199.786 234.236 196.917 223.635 196.916C213.255 196.916 206.464 199.689 203.262 205.235C199.949 210.59 201.054 218.049 206.575 227.612L141.975 227.61C132.699 211.544 128.723 197.2 130.048 184.577C131.593 171.954 137.887 162.2 148.93 155.315C160.083 148.239 174.935 144.701 193.487 144.702C212.922 144.703 232.192 148.242 251.296 155.319C270.289 162.205 287.627 171.96 303.308 184.583C318.989 197.207 331.468 211.552 340.745 227.618C349.358 242.536 353.169 255.637 352.175 266.921C351.403 278.205 347.704 288.055 341.078 296.47C334.674 304.885 324.736 315.213 311.264 327.453L393.422 327.456L422.246 377.375L233.415 377.368L204.592 327.449Z" fill="#391800" stroke="#FF750F" stroke-width="1"/>
                                <path d="M25.447 198.058L2.58852 198.057L-26.4005 147.851L59.4015 147.854L191.923 377.368L128.979 377.365L25.447 198.058Z" fill="#391800" stroke="#FF750F" stroke-width="1"/>
                            </g>
                        </svg>
                    </div>

                    <div class="graphic-overlay"></div>
                </div>
            </div>
        </main>
    </div>

    <div class="footer-spacer"></div>
</body>
</html>
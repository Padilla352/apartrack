<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - APARTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(145deg, #f4f9ff 0%, #e9f2fa 100%);
            min-height: 100vh;
        }

        /* Back button container */
        .back-nav {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.5rem 1.8rem 0 1.8rem;
        }
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 60px;
            padding: 0.6rem 1.4rem;
            color: #1e40af;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        .back-button i {
            font-size: 1rem;
            transition: transform 0.2s;
        }
        .back-button:hover {
            background: #eff6ff;
            border-color: #3b82f6;
            transform: translateX(-4px);
        }
        .back-button:hover i {
            transform: translateX(-3px);
        }

        .about-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.5rem 1.8rem 2rem 1.8rem;
            animation: fadeInUp 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hero Section */
        .about-hero {
            background: linear-gradient(105deg, #1e40af, #3b82f6);
            border-radius: 36px;
            padding: 3rem 2.5rem;
            margin-bottom: 3rem;
            text-align: center;
            color: white;
            box-shadow: 0 15px 30px -12px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .about-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -20%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }

        .about-hero h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .about-hero p {
            font-size: 1.1rem;
            font-weight: 500;
            opacity: 0.92;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 28px;
            padding: 1.8rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(59, 130, 246, 0.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #3b82f6;
            box-shadow: 0 20px 30px -12px rgba(59, 130, 246, 0.2);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e40af;
            margin-bottom: 0.3rem;
        }

        .stat-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.3px;
        }

        /* Two Column Layout (Mission & Vision) */
        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.8rem;
            margin-bottom: 3rem;
        }

        .mv-card {
            background: white;
            border-radius: 28px;
            padding: 2rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(59, 130, 246, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        .mv-card:hover {
            transform: translateY(-4px);
            border-color: #3b82f6;
            box-shadow: 0 15px 25px -12px rgba(59, 130, 246, 0.15);
        }

        .mv-icon {
            width: 56px;
            height: 56px;
            background: #eff6ff;
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
        }

        .mv-icon i {
            font-size: 1.8rem;
            color: #3b82f6;
        }

        .mv-card h3 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }

        .mv-card p {
            color: #475569;
            line-height: 1.6;
            font-weight: 500;
        }

        /* Features Grid */
        .features-section {
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 1.5rem;
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .feature-card {
            background: white;
            border-radius: 24px;
            padding: 1.8rem 1.2rem;
            text-align: center;
            transition: all 0.25s ease;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: #3b82f6;
            box-shadow: 0 15px 30px -10px rgba(59, 130, 246, 0.2);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: #eef2ff;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .feature-icon i {
            font-size: 2rem;
            color: #3b82f6;
        }

        .feature-card h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Team Section - Uniform card layout */
        .team-section {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.8rem;
        }

        .team-card {
            background: white;
            border-radius: 28px;
            padding: 1.8rem 1.2rem;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid rgba(59, 130, 246, 0.1);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            height: 100%;  /* Ensure all cards stretch to same height */
        }

        .team-card:hover {
            transform: translateY(-6px);
            border-color: #3b82f6;
            box-shadow: 0 20px 30px -12px rgba(59, 130, 246, 0.2);
        }

        /* Fixed avatar container - identical size for all */
        .team-avatar {
            width: 120px;
            height: 120px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            overflow: hidden;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #3b82f6;
            flex-shrink: 0;
        }

        .team-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;  /* Ensures images fill the circle evenly */
        }

        /* Fallback icon styling (when image fails) */
        .team-avatar i {
            font-size: 3.5rem;
            color: #3b82f6;
        }

        .team-card h4 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.2rem;
            line-height: 1.3;
        }

        .team-role {
            font-size: 0.8rem;
            color: #3b82f6;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .team-bio {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
            line-height: 1.4;
            margin-bottom: 0.8rem;
            flex-grow: 1;  /* Pushes content to fill space, keeping cards equal height */
        }

        .team-social {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .team-social a {
            color: #94a3b8;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .team-social a:hover {
            color: #3b82f6;
            transform: translateY(-2px);
        }

        /* Responsive for 8 cards */
        @media (max-width: 1100px) {
            .team-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .team-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .about-container {
                padding: 1rem 1.2rem 2rem;
            }
            .back-nav {
                padding: 1rem 1.2rem 0;
            }
            .about-hero h1 {
                font-size: 2rem;
            }
            .stats-row {
                grid-template-columns: 1fr;
            }
            .mission-vision {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 550px) {
            .team-grid {
                grid-template-columns: 1fr;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .team-avatar {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>

<!-- Back Button Section (no navbar) -->
<div class="back-nav">
    <a href="{{ auth()->check() ? route('dashboard') : route('explore') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to {{ auth()->check() ? 'Dashboard' : 'Explore' }}
    </a>
</div>

<div class="about-container">
    <!-- Hero Section -->
    <div class="about-hero">
        <h1>About APARTrack</h1>
        <p>Your Premier Apartment Management Solution — Streamlining property management with modern technology.</p>
    </div>

    <!-- Mission & Vision -->
    <div class="mission-vision">
        <div class="mv-card">
            <div class="mv-icon"><i class="fas fa-bullseye"></i></div>
            <h3>Our Mission</h3>
            <p>To simplify apartment management through innovative technology, making it easier for property managers to focus on what matters most — providing excellent service to their tenants and maximizing operational efficiency.</p>
        </div>
        <div class="mv-card">
            <div class="mv-icon"><i class="fas fa-eye"></i></div>
            <h3>Our Vision</h3>
            <p>To become the leading apartment management platform in the Philippines, empowering every barangay and property owner with smart, accessible, and user‑friendly tools.</p>
        </div>
    </div>

    <!-- Core Features -->
    <div class="features-section">
        <h2 class="section-title">What We Offer</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-building"></i></div>
                <h4>Property Management</h4>
                <p>Centralized dashboard for all your properties, units, and occupancy tracking.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h4>Tenant Tracking</h4>
                <p>Manage tenant information, lease agreements, and payment history seamlessly.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-tools"></i></div>
                <h4>Maintenance Requests</h4>
                <p>Tenants can submit requests; landlords can assign and track repairs easily.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h4>Analytics & Reports</h4>
                <p>Real‑time insights on occupancy, revenue, and property performance.</p>
            </div>
        </div>
    </div>

    <!-- Team Section - 8 Members with uniform image sizes -->
    <div class="team-section">
        <h2 class="section-title">Meet Our Capstone Team</h2>
        <div class="team-grid">
            <!-- Member 1 -->
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/member1.jpg') }}" alt="Reymark Joaquin Mendaros" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>';">
                </div>
                <h4>Reymark Joaquin Mendaros</h4>
                <div class="team-role">Leader</div>
                <div class="team-bio">
                    Leads the team, manages project planning, coordinates tasks, and ensures milestones are achieved on schedule.
                </div>
            </div>

            <!-- Member 2 -->
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/member2.jpg') }}" alt="Carlo D. Nieto" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>';">
                </div>
                <h4>Carlo D. Nieto</h4>
                <div class="team-role">Junior Programmer</div>
               <div class="team-bio">
    Assists in developing and maintaining system functions while supporting the implementation of application features.
</div>
            </div>

            <!-- Member 3 -->
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/member3.jpg') }}" alt="Larenz A. Guillarte" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>';">
                </div>
                <h4>Larenz A. Guillarte</h4>
                <div class="team-role">Junior Programmer</div>
                <div class="team-bio">
                    Supports frontend and backend development while helping improve system functionality and performance.
                </div>
            </div>

            <!-- Member 4 -->
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/member6.jpg') }}" alt="Ronalyn P. Morada" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>';">
                </div>
                <h4>Kim Adrian Delos Santos</h4>
                <div class="team-role">Junior Programmer</div>
                <div class="team-bio">
                    Develops core system functions, writes application code, and improves overall system performance.
                </div>
            </div>

            <!-- Member 5 -->
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/member4.jpg') }}" alt="Jessy Mae H. Calderon" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>';">
                </div>
                <h4>Ronalyn P. Morada</h4>
                <div class="team-role">Researcher</div>
                <div class="team-bio">
    Contributed extensively to research activities through information gathering, analysis, and supporting the overall progress of the project.
</div>
            </div>

            <!-- Member 6 -->
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/member5.jpg') }}" alt="Kim Adrian Delos Santos" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>';">
                </div>
                <h4>Jessy Mae H. Calderon</h4>
                <div class="team-role">Researcher</div>
                <div class="team-bio">
                    Conducts data gathering, analyzes related studies, and supports documentation for the project.
                </div>
            </div>

            <!-- Member 7 -->
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/member7.jpg') }}" alt="Ladylyn P. Ferrer" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>';">
                </div>
                <h4>Ladylyn P. Ferrer</h4>
                <div class="team-role">Documentation</div>
                <div class="team-bio">
                    Prepares reports, organizes project documentation, and ensures records are complete and accurate.
                </div>
            </div>

            <!-- Member 8 -->
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/member8.jpg') }}" alt="Kyle Jude P. Padilla" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>';">
                </div>
                <h4>Kyle Jude P. Padilla</h4>
                <div class="team-role">Mobile Developer</div>
                <div class="team-bio">
                    Designs and develops mobile application features while ensuring usability and smooth performance.
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
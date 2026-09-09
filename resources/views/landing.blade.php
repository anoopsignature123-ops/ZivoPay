            <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description"
        content="Dex Trade — Trade • Invest • Grow. Smarter Trading, Bigger Opportunities, A Stronger Tomorrow." />
    <title>Dex Trade | Trade • Invest • Grow</title>
    <link rel="icon" type="image/png" href="{{ asset('website/assets/images/logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('website/assets/css/style.css') }}" />
    </head>

<body>

    <!-- ============================================================
                             NAVBAR
                             ============================================================ -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#home" class="nav-logo">
                <img src="{{ asset('website/assets/images/logo.png') }}" alt="Dex Trade Logo" />
            </a>
            <div class="nav-links" id="navLinks">
                <a href="#home">Home</a>
                <a href="#forex">Forex</a>
                <a href="#crypto">Crypto</a>

                <a href="#market-research">Market Research</a>

                <a href="#arbitrage">Arbitrage Trading</a>
                <a href="#faq">FAQs</a>
                <div class="nav-cta-mobile-row">
                    <a href="{{ route('user.login') }}" class="nav-cta nav-cta-mobile">Sign Up</a>
                </div>
                <div class="nav-cta-mobile-row">
                    <a href="{{ route('user.register') }}" class="nav-cta nav-cta-mobile">Sign In</a>
                </div>
                </div>
                <a href="{{ route('user.login') }}" class="nav-cta nav-cta-desktop">Sign Up</a>
                <a href="{{ route('user.register') }}" class="nav-cta nav-cta-desktop">Sign In</a>

                <div class="nav-toggle" id="navToggle" aria-label="Toggle Menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </div>
                </div>
                </nav>

    <!-- ============================================================
                             PAGE 01 — HERO  (REDESIGNED v2 — UNIQUE / PREMIUM)
                             ============================================================ -->
    <section class="hero" id="home">

        <!-- Layered background atmosphere -->
        <div class="hero-noise"></div>
        <div class="particles" id="particles"></div>
        <div class="hero-grid-lines"></div>

        <!-- Ambient gradient blobs -->
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="hero-blob hero-blob-3"></div>

        <!-- Horizontal scan line -->
        <div class="hero-scan-line"></div>

        <!-- ═══ MAIN CONTENT ═══ -->
        <div class="hero-v2-wrap">


            <!-- ── CENTER STAGE: logo + headline ── -->
            <div class="hero-stage">

                <!-- Left decorative line -->
                <div class="hero-stage-line hero-stage-line--left reveal-left"></div>

                <!-- Core content -->
                <div class="hero-stage-core">

                    <!-- Logo coin -->
                    <div class="hero-logo-frame reveal">
                        <div class="hlf-rings">
                            <div class="hlf-ring hlf-ring-1"></div>
                            <div class="hlf-ring hlf-ring-2"></div>
                            <div class="hlf-ring hlf-ring-3"></div>
                        </div>
                        <div class="hlf-orbit-dots">
                            <div class="hlf-odot hlf-odot-1"></div>
                            <div class="hlf-odot hlf-odot-2"></div>
                            <div class="hlf-odot hlf-odot-3"></div>
                        </div>
                        <div class="hlf-coin-wrap">
                            <img src="{{ asset('website/assets/images/mainlogo.png') }}" alt="Dex Trade Logo" class="hlf-coin-img" />
                            <div class="hlf-shine"></div>
                        </div>
                        <!-- Floating micro chips around logo -->
                        <div class="hlf-chip hlf-chip-1">
                            <img src="{{ asset('website/assets/icons/lucide/trending-up.svg') }}" alt="" />
                            <span>Smart Income</span>
                        </div>
                        <div class="hlf-chip hlf-chip-2">
                            <img src="{{ asset('website/assets/icons/lucide/coins.svg') }}" alt="" />
                            <span>Crypto</span>
                        </div>
                        <div class="hlf-chip hlf-chip-3">
                            <img src="{{ asset('website/assets/icons/lucide/globe.svg') }} " alt="" />
                            <span>Global</span>
                        </div>
                    </div>

                    <!-- Headline block -->
                    <div class="hero-headline-block reveal">
                        <h1 class="hero-h1">
                            <span class="hero-h1-top">NEXT GEN</span>
                            <span class="hero-h1-bottom">
                                <span class="hero-h1-fx">FOREX</span>
                                <span class="hero-h1-underline"></span>
                            </span>
                        </h1>
                        <p class="hero-sub">
                            Trade Today. Earn Tomorrow.<br />
                            <strong>Live Your Freedom.</strong>
                        </p>
                        <p class="hero-tagline-dots">
                            <span>GROW TOGETHER</span>
                            <span class="htd-sep">◆</span>
                            <span>EARN TOGETHER</span>
                            <span class="htd-sep">◆</span>
                            <span>WIN TOGETHER</span>
                        </p>

                        <!-- CTA row -->
                        <div class="hero-v2-cta">
                            <a href="#about" class="hv2-btn-primary">
                                <span class="hv2-btn-bg"></span>
                                <img src="{{ asset('website/assets/icons/lucide/rocket.svg') }}" alt="" />
                                <span>Get Started</span>
                                <svg class="hv2-btn-arrow" viewBox="0 0 16 16" fill="none">
                                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="#sectors" class="hv2-btn-outline">
                                <span>Explore Platform</span>
                                <img src="{{ asset('website/assets/icons/lucide/external-link.svg') }}" alt="" />
                            </a>
                        </div>
                        </div>

                </div>

                <!-- Right decorative line -->
                <div class="hero-stage-line hero-stage-line--right reveal-right"></div>

            </div>


        </div><!-- /hero-v2-wrap -->

        <!-- Scroll cue -->
        <div class="hero-scroll-cue">
            <div class="hsc-line"></div>
            <span>Scroll</span>
        </div>

    </section>

    <!-- ============================================================
                             PAGE 02 — ABOUT NEXT GEN FOREX
                             ============================================================ -->
    <section class="about" id="about">
        <div class="about-container">
    
            <div class="about-img-wrap reveal-left">
                <img src="{{ asset('website/assets/images/icons.png') }}" alt="About Dex Trade"
                    onerror="this.src='{{ asset('website/assets/images/image1.png') }}'" />
            </div>
            <div class="about-text reveal-right">
                <span class="section-tag">
                    <img src="{{ asset('website/assets/icons/lucide/info.svg') }}" alt="" class="tag-icon" />
                    Company Overview
                </span>
                <h2 class="section-title">Your Gateway to<br /><span class="about-accent">Global Financial
                        Markets</span></h2>
            
                <!-- Block 1 — Gateway -->
                <div class="about-overview-block">
                    <div class="aob-header">
                        <div class="aob-icon">
                            <img src="{{ asset('website/assets/icons/lucide/globe.svg') }}" alt="Gateway" />
                        </div>
                        <h4 class="aob-title">A Gateway to Global Financial Markets</h4>
                        </div>
                        <p class="aob-desc">
                            Headquartered in the United States, Dex Trade Trading is a forward-thinking
                            financial market brand dedicated to redefining how individuals and institutions
                            interact with the global economy. We are built on the belief that financial
                            independence should be accessible to anyone willing to learn and take action.
                            As a premier hub for modern trading, we provide a secure, transparent, and
                            highly efficient gateway to the world's most dynamic financial ecosystems. Our
                            primary focus is to empower our clients by giving them the clarity, support, and
                            tools they need to navigate the complexities of global finance with complete confidence.
                        </p>
                        </div>

            </div>
            </div>
            </section>

    <!-- ============================================================
                             PAGE 03 — OUR BUSINESS SECTORS
                             ============================================================ -->
    <section class="sectors-section" id="sectors">
        <div class="sectors-bg-overlay"></div>
        <div class="sectors-glow-top"></div>
    
        <div class="sectors-container">
            <!-- Header -->
            <div class="text-center reveal">
                <span class="section-tag">
                    <img src="{{ asset('website/assets/icons/lucide/layers.svg') }}" alt="" class="tag-icon" />
                    What We Cover
                </span>
                <h2 class="section-title">Our Business <span class="sectors-accent">Sectors</span></h2>
                <div class="divider"></div>
                <p class="section-subtitle">
                    Dex Trade operates across six powerful domains — giving you a complete edge in the world of
                    digital
                    finance.
                </p>
            </div>

            <!-- Sectors Grid -->
            <div class="sectors-grid">
                <!-- 1. Forex -->
                <div class="sector-card reveal" style="--delay:0s" id="forex">
                    <div class="sector-card-inner">
                        <div class="sector-icon-wrap">
                            <div class="sector-icon">
                                <img src="{{ asset('website/assets/icons/lucide/dollar-sign.svg') }}" alt="Forex" />
                            </div>
                            <div class="sector-icon-ring"></div>
                        </div>
                        <h3 class="sector-title">Forex</h3>
                        <p class="sector-desc">Trade the world's largest financial market. We provide structured
                            insights into global currency
                            pairs, exchange dynamics, and market movement strategies.</p>
                        <div class="sector-tag">Currency Markets</div>
                    </div>
                    <div class="sector-card-glow"></div>
                </div>

                <!-- 2. Crypto -->
                <div class="sector-card reveal" style="--delay:0.08s" id="crypto">
                    <div class="sector-card-inner">
                        <div class="sector-icon-wrap">
                            <div class="sector-icon sector-icon--green">
                                <img src="{{ asset('website/assets/icons/lucide/coins.svg') }}" alt="Crypto" />
                            </div>
                            <div class="sector-icon-ring sector-icon-ring--green"></div>
                            </div>
                            <h3 class="sector-title">Crypto</h3>
                            <p class="sector-desc">Navigate the digital asset revolution with confidence. From Bitcoin to
                                altcoins — we
                                break down blockchain fundamentals and crypto market cycles.</p>
                            <div class="sector-tag sector-tag--green">Digital Assets</div>
                            </div>
                            <div class="sector-card-glow sector-card-glow--green"></div>
                </div>

                <!-- 3. Financial Technology -->
                <div class="sector-card reveal" style="--delay:0.16s" id="fintech">
                    <div class="sector-card-inner">
                        <div class="sector-icon-wrap">
                            <div class="sector-icon">
                                <img src="{{ asset('website/assets/icons/lucide/cpu.svg') }}" alt="FinTech" />
                            </div>
                            <div class="sector-icon-ring"></div>
                        </div>
                        <h3 class="sector-title">Financial Technology</h3>
                        <p class="sector-desc">Leverage the power of modern FinTech innovations — from automated trading
                            systems and
                            smart contracts to next-generation payment infrastructure.</p>
                        <div class="sector-tag">FinTech</div>
                    </div>
                    <div class="sector-card-glow"></div>
                </div>

                <!-- 4. Market Research -->
                <div class="sector-card reveal" style="--delay:0.24s" id="market-research">
                    <div class="sector-card-inner">
                        <div class="sector-icon-wrap">
                            <div class="sector-icon sector-icon--green">
                                <img src="{{ asset('website/assets/icons/lucide/activity.svg') }}" alt="Market Research" />
                            </div>
                            <div class="sector-icon-ring sector-icon-ring--green"></div>
                        </div>
                        <h3 class="sector-title">Market Research</h3>
                        <p class="sector-desc">Decisions built on data, not guesswork. Our in-depth market analysis
                            covers trend identification,
                            economic indicators, and global capital flow patterns.</p>
                        <div class="sector-tag sector-tag--green">Data & Analytics</div>
                    </div>
                    <div class="sector-card-glow sector-card-glow--green"></div>
                </div>
                <!-- 5. Education -->
                <div class="sector-card reveal" style="--delay:0.32s" id="education">
                    <div class="sector-card-inner">
                        <div class="sector-icon-wrap">
                            <div class="sector-icon">
                                <img src="{{ asset('website/assets/icons/lucide/book-open.svg') }}" alt="Education" />
                            </div>
                            <div class="sector-icon-ring"></div>
                        </div>
                        <h3 class="sector-title">Education</h3>
                        <p class="sector-desc">Knowledge is your most powerful asset. From beginner fundamentals to
                            advanced trading
                            strategies — our structured learning content empowers every participant.</p>
                        <div class="sector-tag">Learn & Grow</div>
                    </div>
                    <div class="sector-card-glow"></div>
                </div>

                <!-- 6. Arbitrage Trading -->
                <div class="sector-card reveal" style="--delay:0.40s" id="arbitrage">
                    <div class="sector-card-inner">
                        <div class="sector-icon-wrap">
                            <div class="sector-icon sector-icon--green">
                                <img src="{{ asset('website/assets/icons/lucide/arrow-right.svg') }}" alt="Arbitrage" />
                            </div>
                            <div class="sector-icon-ring sector-icon-ring--green"></div>
                            </div>
                        <h3 class="sector-title">Arbitrage Trading</h3>
                        <p class="sector-desc">Profit from price differentials across markets and exchanges. We provide
                            structured
                            approaches to identify and act on arbitrage opportunities in real time.</p>
                        <div class="sector-tag sector-tag--green">Smart Execution</div>
                        </div>
                    <div class="sector-card-glow sector-card-glow--green"></div>
                    </div>

            </div>
            
            
            </div>
            </section>
            
            <!-- ============================================================
                                                                                         PAGE 04 — WHAT IS CRYPTOCURRENCY
                                                                                         ============================================================ -->
            <section class="crypto-explainer" id="cryptocurrency">
                <div class="ce-bg-overlay"></div>
                <div class="ce-container">
            
                    <div class="text-center reveal">
                        <span class="section-tag">
                            Understanding Cryptocurrency
                        </span>
                        <h2 class="section-title">What is<br />Cryptocurrency?</h2>
                        <div class="divider"></div>
                        <p class="section-subtitle">
                            Cryptocurrency is a form of digital asset that uses cryptographic technology to secure
                            transactions and manage the creation or transfer of digital units.
                        </p>
                    </div>
            
                    <div class="ce-layout">
            
                        <!-- Left: Key Characteristics -->
                        <div class="ce-left reveal-left">
                            <h3 class="ce-sub-heading">Key Characteristics</h3>
                            <div class="ce-chars">
            
                                <div class="ce-char-item">
                                    <div class="ce-char-icon">
                                        <img src="{{ asset('website/assets/icons/lucide/monitor.svg') }}" alt="Digital" />
                                    </div>
                                    <div>
                                <h4>Digital</h4>
                                <p>Exists in digital form and can be transferred electronically.</p>
                                </div>
                                </div>
                                
                                <div class="ce-char-item">
                                    <div class="ce-char-icon">
                                        <img src="{{ asset('website/assets/icons/lucide/share-2.svg') }}" alt="Decentralized" />
                                    </div>
                                    <div>
                                <h4>Decentralized Technology</h4>
                                <p>Many cryptocurrencies operate using blockchain networks rather than a single central
                                    database.</p>
                                </div>
                                </div>
                                
                                <div class="ce-char-item">
                                    <div class="ce-char-icon">
                                        <img src="{{ asset('website/assets/icons/lucide/link-2.svg') }}" alt="Blockchain" />
                                    </div>
                                    <div>
                                <h4>Blockchain Based</h4>
                                <p>Transactions can be recorded on a distributed ledger.</p>
                                </div>
                                </div>

                        <div class="ce-char-item">
                            <div class="ce-char-icon">
                                <img src="{{ asset('website/assets/icons/lucide/shield-check.svg') }}" alt="Secure" />
                            </div>
                            <div>
                                <h4>Secure Technology</h4>
                                <p>Cryptographic techniques are used to secure transactions and wallets.</p>
                            </div>
                        </div>

                    </div>
                    </div>

                <!-- Right: Visual + Examples -->
                <div class="ce-right reveal-right">
                    <div class="ce-visual-block">
                        <img src="{{ asset('website/assets/images/11.png') }}" alt="Cryptocurrency"
                            onerror="this.style.display='none'" />
                        <div class="ce-visual-glow"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================================
                             PAGE 05 — BENEFITS OF CRYPTOCURRENCY
                             ============================================================ -->
    <section class="benefits-section vs11" id="benefits">

        <div class="ben-glow-line"></div>
        <div class="ben-bg-overlay"></div>
        
        <div class="ben-container">
        
            <div class="text-center reveal">
                <span class="section-tag">
                    <img src="assets/icons/lucide/star.svg" alt="" class="tag-icon" />
                    Why Cryptocurrency
                </span>
                <h2 class="section-title">Benefits of<br /><span class="ben-accent">Cryptocurrency</span></h2>
                <div class="divider"></div>
                <p class="section-subtitle">
                    Discover why crypto is reshaping global finance — and why it matters for your future.
                </p>
                </div>

            <!-- ROW 1 — image LEFT, content RIGHT -->
            <div class="ben-row reveal-left">
                <div class="ben-img-col">
                    <div class="ben-img-wrap">
                        <img src="{{ asset('website/assets/images/mobileview.png') }}" alt="Decentralized Finance" />
                        <div class="ben-img-glow"></div>
                    </div>
                </div>
                <div class="ben-content-col">
                    <h3 class="ben-heading">Decentralized &amp;<br />Fast Transactions</h3>
                    <p class="ben-desc">Cryptocurrency operates without a central authority — no banks, no borders, no
                        delays.
                        Peer-to-peer transfers happen in seconds, anywhere in the world, 24 hours a day.</p>
                    <div class="ben-points">
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--amber">
                                <img src="{{ asset('website/assets/icons/lucide/globe.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">No Central Control</span>
                                <span class="ben-point-sub">Operate freely across all borders without
                                    intermediaries</span>
                            </div>
                        </div>
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--teal">
                                <img src="{{ asset('website/assets/icons/lucide/zap.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">Lightning Fast</span>
                                <span class="ben-point-sub">Transactions confirm in seconds, not days</span>
                            </div>
                        </div>
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--amber">
                                <img src="{{ asset('website/assets/icons/lucide/dollar-sign.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">Low Fees</span>
                                <span class="ben-point-sub">Minimal transaction costs compared to traditional
                                    banking</span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                </div>
                </section>

    <!-- ============================================================
                             PAGE 06 — BENEFITS OF CRYPTOCURRENCY (Security)
                             ============================================================ -->
    <section class="benefits-section vs12" id="benefits">
    
        <div class="ben-glow-line"></div>
        <div class="ben-bg-overlay"></div>
    
        <div class="ben-container">
    
            <!-- ROW 2 — content LEFT, image RIGHT -->
            <div class="ben-row ben-row--reverse reveal-right">
                <div class="ben-img-col">
                    <div class="ben-img-wrap">
                        <img src="{{ asset('website/assets/images/image101.png') }}" alt="Security &amp; Transparency" />
                        <div class="ben-img-glow ben-img-glow--amber"></div>
                    </div>
                </div>
                <div class="ben-content-col">
                    <h3 class="ben-heading">Secure by Design &amp;<br />Fully Transparent</h3>
                    <p class="ben-desc">Every transaction is protected by cryptographic technology and recorded on an
                        immutable public
                        blockchain. Nothing is hidden — everything is verifiable by anyone, at any time.</p>
                    <div class="ben-points">
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--teal">
                                <img src="{{ asset('website/assets/icons/lucide/lock.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">Cryptographic Security</span>
                                <span class="ben-point-sub">Tamper-proof and resistant to fraud at every level</span>
                            </div>
                        </div>
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--amber">
                                <img src="{{ asset('website/assets/icons/lucide/eye.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">Open Ledger</span>
                                <span class="ben-point-sub">All transactions visible and verifiable on the
                                    blockchain</span>
                            </div>
                        </div>
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--teal">
                                <img src="{{ asset('website/assets/icons/lucide/link-2.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">Immutable Records</span>
                                <span class="ben-point-sub">Once recorded, data cannot be altered or deleted</span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                
                </div>
                </section>
    
    <!-- ============================================================
                             PAGE 07 — BENEFITS OF CRYPTOCURRENCY (Growth)
                             ============================================================ -->
    <section class="benefits-section vs13" id="benefits">
    
        <div class="ben-glow-line"></div>
        <div class="ben-bg-overlay"></div>
    
        <div class="ben-container">
    
            <!-- ROW 3 — image LEFT, content RIGHT -->
            <div class="ben-row reveal-left">
                <div class="ben-img-col">
                    <div class="ben-img-wrap">
                        <img src="{{ asset('website/assets/images/image102.png') }}" alt="Growth &amp; Inclusion" />
                        <div class="ben-img-glow"></div>
                    </div>
                </div>
                <div class="ben-content-col">
                    <h3 class="ben-heading">Growth Potential &amp;<br />Financial Inclusion</h3>
                    <p class="ben-desc">Digital assets have created new wealth globally while opening financial services
                        to billions
                        of unbanked individuals. All you need is a smartphone — no bank account required.</p>
                    <div class="ben-points">
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--amber">
                                <img src="{{ asset('website/assets/icons/lucide/trending-up.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">High Growth Asset Class</span>
                                <span class="ben-point-sub">Demonstrated substantial returns over the long term</span>
                            </div>
                        </div>
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--teal">
                                <img src="{{ asset('website/assets/icons/lucide/users.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">Open to Everyone</span>
                                <span class="ben-point-sub">No bank account needed — just a phone and connection</span>
                            </div>
                        </div>
                        <div class="ben-point">
                            <div class="ben-point-icon ben-point-icon--amber">
                                <img src="{{ asset('website/assets/icons/lucide/award.svg') }}" alt="" />
                            </div>
                            <div>
                                <span class="ben-point-title">Own Your Assets</span>
                                <span class="ben-point-sub">Full ownership with no third-party control over your
                                    funds</span>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>

        </div>
    </section>
    
    
    <!-- ============================================================
                             PAGE 08 — WHY FOREX &amp; CRYPTO WITH US
                             ============================================================ -->
    <section class="why-us-section" id="why-us">
        <div class="why-us-bg-overlay"></div>
    
        <div class="why-us-container">
    
            <!-- Header -->
            <div class="text-center reveal">
                <span class="section-tag">
                    <img src="{{ asset('website/assets/icons/lucide/star.svg') }}" alt="" class="tag-icon" />
                    Why Choose Us
                </span>
                <h2 class="section-title">Why Forex &amp; Crypto<br /><span class="why-accent">With Us?</span></h2>
                <div class="divider"></div>
                <p class="section-subtitle">
                    Everything you need to understand and explore the Forex &amp; crypto world — accurate, fast and
                    trusted.
                </p>
            </div>

            <!-- 6 Feature Cards -->
            <div class="why-us-grid">
            
                <!-- 1 -->
                <div class="why-card reveal" style="--delay:0s">
                    <div class="why-card-top">
                        <div class="why-card-icon-wrap">
                            <div class="why-card-icon">
                                <img src="{{ asset('website/assets/icons/lucide/activity.svg') }}" alt="Real-Time" />
                            </div>
                            <div class="why-icon-glow"></div>
                            </div>
                            </div>
                            <h3>Real-Time Market Data</h3>
                            <p>Stay ahead with live price feeds, market cap updates and trading volume data across all major
                                Forex pairs and
                                cryptocurrencies — refreshed continuously.</p>
                            <span class="why-card-tag">Live Updates</span>
                </div>

                <!-- 2 -->
                <div class="why-card reveal" style="--delay:0.1s">
                    <div class="why-card-top">
                        <div class="why-card-icon-wrap">
                            <div class="why-card-icon why-card-icon--amber">
                                <img src="{{ asset('website/assets/icons/lucide/shield-check.svg') }}" alt="Secure" />
                            </div>
                            <div class="why-icon-glow why-icon-glow--amber"></div>
                        </div>
                    </div>
                    <h3>Secure Platform</h3>
                    <p>Built with industry-grade security protocols to protect your data and digital assets at every
                        step.</p>
                    <span class="why-card-tag why-card-tag--amber">Encrypted</span>
                </div>
                <!-- 3 -->
                <div class="why-card reveal" style="--delay:0.2s">
                    <div class="why-card-top">
                        <div class="why-card-icon-wrap">
                            <div class="why-card-icon">
                                <img src="{{ asset('website/assets/icons/lucide/book-open.svg') }}" alt="Easy" />
                            </div>
                            <div class="why-icon-glow"></div>
                            </div>
                            </div>
                    <h3>Easy to Understand</h3>
                    <p>Complex Forex and crypto concepts broken down into simple, clear and actionable insights for
                        everyone — beginners to
                        experts.</p>
                    <span class="why-card-tag">Beginner Friendly</span>
                </div>

                <!-- 4 -->
                <div class="why-card reveal" style="--delay:0.3s">
                    <div class="why-card-top">
                        <div class="why-card-icon-wrap">
                            <div class="why-card-icon why-card-icon--amber">
                                <img src="{{ asset('website/assets/icons/lucide/globe.svg') }}" alt="Global" />
                            </div>
                            <div class="why-icon-glow why-icon-glow--amber"></div>
                            </div>
                    </div>
                    <h3>Global Market Insights</h3>
                    <p>Access worldwide Forex and crypto market trends, regional data and international movement
                        analysis from one place.
                    </p>
                    <span class="why-card-tag why-card-tag--amber">Worldwide</span>
                    </div>

                <!-- 5 -->
                <div class="why-card reveal" style="--delay:0.4s">
                    <div class="why-card-top">
                        <div class="why-card-icon-wrap">
                            <div class="why-card-icon">
                                <img src="{{ asset('website/assets/icons/lucide/zap.svg') }}" alt="Fast" />
                            </div>
                            <div class="why-icon-glow"></div>
                            </div>
                            </div>
                            <h3>Fast Updates</h3>
                            <p>Lightning-speed data delivery ensures you always have the most current information when it
                                matters most.</p>
                            <span class="why-card-tag">Low Latency</span>
                            </div>

                <!-- 6 -->
                <div class="why-card reveal" style="--delay:0.5s">
                    <div class="why-card-top">
                        <div class="why-card-icon-wrap">
                            <div class="why-card-icon why-card-icon--amber">
                                <img src="{{ asset('website/assets/icons/lucide/badge-check.svg') }}" alt="Trusted" />
                            </div>
                            <div class="why-icon-glow why-icon-glow--amber"></div>
                            </div>
                    </div>
                    <h3>Trusted Information</h3>
                    <p>All market data and insights are sourced from verified, reputable channels — giving you full
                        confidence in every
                        decision.</p>
                    <span class="why-card-tag why-card-tag--amber">Verified Sources</span>
                    </div>
                    
                    </div>

        </div>
    </section>
    
    <!-- ============================================================
                                                     PAGE 09 — FAQs
                                                     ============================================================ -->
    <section class="faq-section" id="faq">
        <div class="faq-bg-overlay"></div>
    
        <div class="faq-container">

            <!-- Header -->
            <div class="text-center reveal">
                <span class="section-tag">
                    <img src="{{ asset('website/assets/icons/lucide/help-circle.svg') }}" alt="" class="tag-icon" />
                    FAQs
                </span>
                <h2 class="section-title">Frequently Asked<br /><span class="faq-accent">Questions</span></h2>
                <div class="divider"></div>
                <p class="section-subtitle">
                    Got questions about Dex Trade, cryptocurrency, or the platform? We've got clear answers — right
                    here.
                </p>
            </div>
            <!-- FAQ Grid -->
            <div class="faq-grid">
            
                <!-- Left Column -->
                <div class="faq-col">

                    <div class="faq-item reveal" style="--delay:0s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>What is Dex Trade?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>Dex Trade is a next-generation digital platform built around Forex trading,
                                cryptocurrency, and
                                digital assets. It offers structured earning opportunities through multiple income
                                categories,
                                level-based growth, and a community-driven network — all in one ecosystem.</p>
                        </div>
                    </div>
                    <div class="faq-item reveal" style="--delay:0.05s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>Do I need prior Forex or crypto experience to join?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>No prior experience is required. Dex Trade is designed to be beginner-friendly. We
                                break down complex
                                Forex and crypto concepts into simple, clear, and actionable insights that anyone can
                                understand and act on.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item reveal" style="--delay:0.1s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>What is cryptocurrency?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>Cryptocurrency is a form of digital asset that uses cryptographic technology to secure
                                transactions and
                                manage the creation or transfer of digital units. It operates on decentralized networks
                                like blockchain,
                                making transactions transparent, secure, and borderless.</p>
                        </div>
                    </div>

                    <div class="faq-item reveal" style="--delay:0.15s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>How does the income structure work?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>Dex Trade offers a systematic, multi-category income plan. Participants can explore
                                level-based growth,
                                performance-based ranks and rewards, and community network earnings. The structure is
                                designed to be
                                transparent, fair, and scalable as your network grows.</p>
                        </div>
                    </div>

                    <div class="faq-item reveal" style="--delay:0.2s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>Is the platform globally accessible?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>Yes. Dex Trade provides access to global Forex and crypto market trends, regional
                                data, and
                                international movement analysis. Participants from across the world can join and benefit
                                from the platform's
                                structured ecosystem.</p>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="faq-col">

                    <div class="faq-item reveal" style="--delay:0.05s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>How is my data and investment secured?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>Dex Trade is built with industry-grade security protocols. Cryptographic techniques
                                are used to
                                secure all transactions and wallets, ensuring your data and digital assets are protected
                                at every step
                                of your journey.</p>
                        </div>
                    </div>

                    <div class="faq-item reveal" style="--delay:0.1s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>What currencies and cryptocurrencies does Dex Trade support?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>The platform covers major Forex currency pairs alongside a wide range of top
                                cryptocurrencies including
                                Bitcoin (BTC), Ethereum (ETH), BNB, Solana (SOL), XRP, and many more. Our insights span
                                the top 10 most
                                recognized digital assets in the global market.</p>
                        </div>
                    </div>

                    <div class="faq-item reveal" style="--delay:0.15s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>How are performance ranks and rewards determined?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>Ranks and rewards are based on your participation, network growth, and overall
                                performance within the Next
                                Gen Forex ecosystem. The system is structured to reward consistent effort, community
                                building, and long-term
                                commitment to the platform.</p>
                        </div>
                    </div>
                    <div class="faq-item reveal" style="--delay:0.2s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>How often is market data updated?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>Market data is refreshed continuously with live price feeds, market cap updates and
                                trading volume data
                                across all major Forex pairs and cryptocurrencies. Our lightning-speed delivery ensures
                                you always have the
                                most current information when it matters most.</p>
                        </div>
                    </div>

                    <div class="faq-item reveal" style="--delay:0.25s">
                        <button class="faq-question" aria-expanded="false">
                            <span class="faq-q-icon">
                                <img src="{{ asset('website/assets/icons/lucide/circle-help.svg') }}" alt="" />
                            </span>
                            <span>How do I get started with Dex Trade?</span>
                            <span class="faq-chevron">
                                <img src="{{ asset('website/assets/icons/lucide/chevron-down.svg') }}" alt="" />
                            </span>
                        </button>
                        <div class="faq-answer">
                            <p>Getting started is simple. Join the Dex Trade ecosystem, explore the structured
                                earning opportunities
                                available, and begin building your network. The platform is designed to guide you from
                                day one — whether
                                you're a complete beginner or an experienced participant.</p>
                        </div>
                        </div>

                </div>

            </div>

        </div>
        </section>

    <!-- ============================================================
                             FOOTER
                             ============================================================ -->
    <footer class="footer" id="footer">
        <div class="footer-glow-line"></div>

        <!-- CTA Banner -->
        <div class="footer-cta-banner">
            <div class="footer-cta-inner">
                <div class="footer-cta-text">
                    <h3>Ready to <span>Grow Together?</span></h3>
                    <p>Join the Dex Trade ecosystem and start your structured earning journey today.</p>
                </div>
                <a href="#home" class="footer-cta-btn">
                    <img src="{{ asset('website/assets/icons/lucide/rocket.svg') }}" alt="" />
                    Get Started Now
                </a>
            </div>
            </div>
            
            <!-- Main Grid -->
            <div class="footer-main">

            <div class="footer-brand">
                <div class="logo-wrap">
                    <img src="{{ asset('website/assets/images/logo.png') }}" alt="Dex Trade Logo" />
                </div>
                <p>Dex Trade is a next-generation digital platform built around Forex trading,
                    cryptocurrency, and digital assets. One Platform. Multiple Opportunities. Structured Growth.</p>
                <div class="footer-tagline">Grow Together. Earn Together.</div>

                <!-- Social Icons -->
                <div class="footer-socials">
                    <a href="#" class="fsocial-btn" aria-label="Telegram">
                        <img src="{{ asset('website/assets/icons/lucide/send.svg') }}" alt="Telegram" />
                    </a>
                    <a href="#" class="fsocial-btn" aria-label="Twitter">
                        <img src="{{ asset('website/assets/icons/lucide/twitter.svg') }}" alt="Twitter" />
                    </a>
                    <a href="#" class="fsocial-btn" aria-label="Instagram">
                        <img src="{{ asset('website/assets/icons/lucide/instagram.svg') }}" alt="Instagram" />
                    </a>
                    <a href="#" class="fsocial-btn" aria-label="Youtube">
                        <img src="{{ asset('website/assets/icons/lucide/youtube.svg') }}" alt="Youtube" />
                    </a>
                    <a href="#" class="fsocial-btn" aria-label="Globe">
                        <img src="{{ asset('website/assets/icons/lucide/globe.svg') }}" alt="Website" />
                    </a>
                    </div>
            </div>

            <div class="footer-links">
                <h4>Platform</h4>
                <a href="#home">Home</a>
                <a href="#forex">Forex</a>
                <a href="#crypto">Crypto</a>
                <a href="#fintech">Fin Technology</a>
                <a href="#market-research">Market Research</a>
                <a href="#education">Education</a>
                <a href="#arbitrage">Arbitrage Trading</a>
                <a href="#faq">FAQs</a>
            </div>

            <div class="footer-links">
                <h4>Ecosystem</h4>
                <a href="#">Income Categories</a>
                <a href="#">Level-Based Growth</a>
                <a href="#">Performance Ranks</a>
                <a href="#">Community Network</a>
                <a href="#">Digital Assets</a>
            </div>

            <div class="footer-links">
                <h4>Legal</h4>
                <a href="#">Terms of Use</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Risk Disclosure</a>
                <a href="#">Contact Us</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; 2026 Dex Trade. All rights reserved.</span>
            <span class="footer-sep">|</span>
            <span>Smart Digital Income &amp; Growth Platform</span>
        </div>
    </footer>

    <script src="{{ asset('website/assets/js/main.js') }}"></script>
</body>

</html>
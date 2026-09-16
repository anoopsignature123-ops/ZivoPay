<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>ZivoPay – Evening. One Zivo Pay.</title>
<meta name="description"
    content="ZivoPay – India's fastest recharge & bill payment platform. Mobile recharge, DTH, Credit Card, Electricity, Gas, Water and more." />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Rajdhani:wght@600;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
  <link rel="icon" href="{{ asset('web/assets/images/logo.png') }}" />

<style>
    /* ═══════════════════════════════════════════════════════════
     VARIABLES
  ═══════════════════════════════════════════════════════════ */
    :root {
        --green: #00a844;
        --green-d: #007a30;
        --green-dd: #004f20;
        --green-l: #e6f9ee;
        --green-ll: #f0fdf5;
        --grad: linear-gradient(135deg, #00c853 0%, #007a30 100%);
        --hero-bg: #f0fdf5;
        --white: #ffffff;
        --body-bg: #f6f8f6;
        --alt-bg: #eef6f1;
        --card-bg: #ffffff;
        --border: #ddeee4;
        --text: #152b1f;
        --text-b: #3a5447;
        --text-m: #6b8f79;
        --sh: 0 4px 24px rgba(0, 0, 0, .07);
        --sh-g: 0 6px 24px rgba(0, 168, 67, .18);
        --r: 14px;
        --rl: 20px;
    }

    /* ═══════════════════════════════════════════════════════════
     RESET / BASE
  ═══════════════════════════════════════════════════════════ */
    *,
    *::before,
    *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box
    }

    html {
      scroll-behavior: smooth;
      overflow-x: hidden
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--body-bg);
      color: var(--text-b);
      overflow-x: hidden
    }

    a {
      text-decoration: none;
      color: inherit
    }

    img {
      max-width: 100%;
      display: block
    }

    ul {
      list-style: none
    }

    .container {
      width: 90%;
      max-width: 1180px;
      margin: 0 auto
    }

    /* ═══════════════════════════════════════════════════════════
     SHARED HELPERS
  ═══════════════════════════════════════════════════════════ */
    .chip {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      background: var(--green-l);
      color: var(--green-d);
      border: 1px solid rgba(0, 168, 67, .28);
      border-radius: 50px;
      padding: .3rem 1rem;
      font-size: .73rem;
      font-weight: 700;
      letter-spacing: .8px;
      text-transform: uppercase;
      margin-bottom: .9rem
    }

    .ttl {
      font-family: 'Rajdhani', sans-serif;
      font-size: clamp(1.9rem, 3.5vw, 2.65rem);
      font-weight: 700;
      color: var(--text);
      line-height: 1.18;
      text-align: center
    }

    .ttl span {
      color: var(--green)
    }

    .sub {
      text-align: center;
      color: var(--text-m);
      font-size: .98rem;
      margin-top: .5rem;
      margin-bottom: 2.6rem
    }

    .btn-solid {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      background: var(--grad);
      color: #fff;
      padding: .85rem 2rem;
      border-radius: 50px;
      font-weight: 700;
      font-size: .93rem;
      box-shadow: 0 6px 20px rgba(0, 168, 67, .35);
      transition: all .28s;
      white-space: nowrap
    }

    .btn-solid:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 28px rgba(0, 168, 67, .45)
    }

    .btn-line {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      border: 2px solid #fff;
      color: #fff;
      padding: .82rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      font-size: .93rem;
      transition: all .28s;
      white-space: nowrap;
      backdrop-filter: blur(4px);
      background: rgba(255, 255, 255, .08)
    }

    .btn-line:hover {
      background: rgba(255, 255, 255, .2);
      border-color: #fff
    }

    /* ═══════════════════════════════════════════════════════════
     ANNOUNCEMENT BAR
  ═══════════════════════════════════════════════════════════ */
    .topbar {
      background: var(--grad);
      color: #fff;
      text-align: center;
      padding: .42rem 1rem;
      font-size: .77rem;
      font-weight: 600;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis
    }

    .topbar i {
      margin: 0 .4rem;
      opacity: .9
    }

    /* ═══════════════════════════════════════════════════════════
     NAVBAR
  ═══════════════════════════════════════════════════════════ */
    .nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 9999;
      background: rgba(255, 255, 255, .97);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      border-bottom: 1px solid rgba(0, 168, 67, .13);
      box-shadow: 0 2px 14px rgba(0, 0, 0, .055);
      transition: all .3s
    }

    .nav.scrolled {
      box-shadow: 0 4px 22px rgba(0, 168, 67, .11)
    }

    .nav-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: .5rem 0
    }

    .nav-logo img {
      height: 60px;
      width: auto
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 2rem
    }

    .nav-links a {
      color: var(--text-b);
      font-size: .9rem;
      font-weight: 600;
      transition: color .2s
    }

    .nav-links a:hover {
      color: var(--green)
    }

    .nav-cta {
      background: var(--grad);
      color: #fff !important;
      padding: .5rem 1.45rem;
      border-radius: 50px;
      font-weight: 700 !important;
      font-size: .87rem !important;
      box-shadow: 0 4px 14px rgba(0, 168, 67, .3);
      transition: all .25s;
      white-space: nowrap
    }

    .nav-cta:hover {
      box-shadow: 0 6px 20px rgba(0, 168, 67, .45) !important;
      transform: translateY(-1px)
    }

    .hbg {
      display: none;
      flex-direction: column;
      gap: 5px;
      cursor: pointer;
      padding: 5px;
      z-index: 10001
    }

    .hbg span {
      display: block;
      width: 25px;
      height: 2.5px;
      background: var(--green);
      border-radius: 3px;
      transition: all .32s cubic-bezier(.4, 0, .2, 1);
      transform-origin: center
    }

    .hbg.on span:nth-child(1) {
      transform: translateY(7.5px) rotate(45deg)
    }

    .hbg.on span:nth-child(2) {
      opacity: 0;
      transform: scaleX(0)
    }

    .hbg.on span:nth-child(3) {
      transform: translateY(-7.5px) rotate(-45deg)
    }

    /* ═══════════════════════════════════════════════════════════
     MOBILE MENU
  ═══════════════════════════════════════════════════════════ */
    .mmenu {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 9998;
      background: #fff;
      flex-direction: column;
      overflow: hidden
    }

    .mmenu.on {
      display: flex
    }

    .mm-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 1.4rem;
      border-bottom: 1px solid #e6f0ea;
      min-height: 74px;
      flex-shrink: 0
    }

    .mm-head img {
      height: 52px
    }

    .mm-x {
      width: 40px;
      height: 40px;
      background: var(--green-l);
      border: 1px solid rgba(0, 168, 67, .22);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: var(--green);
      font-size: 1.1rem;
      transition: all .2s;
      flex-shrink: 0
    }

    .mm-x:hover {
      background: rgba(0, 168, 67, .18)
    }

    .mm-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow-y: auto
    }

    .mm-body a {
      display: flex;
      align-items: center;
      gap: 1rem;
      color: var(--text);
      font-size: 1.05rem;
      font-weight: 600;
      padding: 1.1rem 2rem;
      border-bottom: 1px solid #f0f7f2;
      transition: all .2s
    }

    .mm-body a i {
      color: var(--green);
      font-size: 1.25rem;
      width: 24px;
      text-align: center;
      flex-shrink: 0
    }

    .mm-body a:hover,
    .mm-body a:active {
      color: var(--green);
      background: rgba(0, 168, 67, .05);
      padding-left: 2.5rem
    }

    .mm-foot {
      padding: 1.4rem 2rem;
      border-top: 1px solid #e6f0ea;
      flex-shrink: 0
    }

    .mm-foot a {
      display: block;
      text-align: center;
      background: var(--grad);
      color: #fff;
      font-size: 1rem;
      font-weight: 700;
      padding: 1rem;
      border-radius: 50px;
      box-shadow: 0 4px 14px rgba(0, 168, 67, .3)
    }

    /* ═══════════════════════════════════════════════════════════
     HERO
  ═══════════════════════════════════════════════════════════ */
    .hero-stripe {
      background: var(--grad);
      position: relative;
      overflow: hidden;
      padding: 5rem 0 3rem
    }

    .hero-stripe::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: linear-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .06) 1px, transparent 1px);
      background-size: 52px 52px;
      pointer-events: none
    }

    .hero-stripe::after {
      content: '';
      position: absolute;
      top: -120px;
      right: -80px;
      width: 420px;
      height: 420px;
      background: radial-gradient(circle, rgba(255, 255, 255, .14) 0%, transparent 68%);
      pointer-events: none
    }

    .hero-inner {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      background: rgba(255, 255, 255, .18);
      color: #fff;
      border: 1px solid rgba(255, 255, 255, .35);
      border-radius: 50px;
      padding: .32rem 1rem;
      font-size: .73rem;
      font-weight: 700;
      letter-spacing: .8px;
      text-transform: uppercase;
      margin-bottom: 1.1rem;
      backdrop-filter: blur(4px)
    }

    .hero-badge i {
      font-size: .75rem
    }

    .hero-h1 {
      font-family: 'Rajdhani', sans-serif;
      font-size: clamp(2.6rem, 5.5vw, 4rem);
      font-weight: 700;
      line-height: 1.05;
      color: #fff;
      margin-bottom: 1.1rem
    }

    .hero-h1 .hi {
      color: #c8ffe0;
      display: block
    }

    .hero-desc {
      color: rgba(255, 255, 255, .84);
      font-size: 1.06rem;
      line-height: 1.72;
      margin-bottom: 1.9rem;
      max-width: 460px
    }

    .hero-desc strong {
      color: #fff
    }

    .hero-btns {
      display: flex;
      gap: .9rem;
      flex-wrap: wrap;
      margin-bottom: 2rem
    }

    .hero-trust {
      display: flex;
      gap: 1.8rem;
      flex-wrap: wrap
    }

    .tr-item {
      display: flex;
      align-items: center;
      gap: .42rem;
      color: rgba(255, 255, 255, .78);
      font-size: .82rem
    }

    .tr-item i {
      color: #c8ffe0;
      font-size: .85rem
    }

    .hero-right {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: flex-end
    }

    /* phone mockup */
    .hero-phone {
      width: 260px;
      background: linear-gradient(160deg, #1c3a27, #0f2018);
      border-radius: 32px;
      border: 2px solid rgba(255, 255, 255, .18);
      box-shadow: 0 24px 64px rgba(0, 0, 0, .35), 0 0 0 1px rgba(255, 255, 255, .06);
      padding: 18px 16px 22px;
      position: relative;
      z-index: 2
    }

    .ph-notch {
      width: 70px;
      height: 20px;
      background: #0a1510;
      border-radius: 0 0 14px 14px;
      margin: 0 auto 14px
    }

    .ph-header {
      background: var(--grad);
      border-radius: 12px;
      padding: 12px 14px;
      margin-bottom: 12px
    }

    .ph-hi {
      font-size: .6rem;
      color: rgba(255, 255, 255, .7);
      margin-bottom: 2px
    }

    .ph-bal {
      font-size: 1.15rem;
      font-weight: 800;
      color: #fff
    }

    .ph-sub {
      font-size: .6rem;
      color: rgba(255, 255, 255, .65)
    }

    .ph-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 8px;
      margin-bottom: 12px
    }

    .ph-ic {
      background: rgba(255, 255, 255, .07);
      border-radius: 10px;
      padding: 8px 4px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, .1)
    }

    .ph-ic i {
      color: #00c853;
      font-size: 1rem;
      display: block;
      margin-bottom: 3px
    }

    .ph-ic span {
      font-size: .44rem;
      color: rgba(255, 255, 255, .65)
    }

    .ph-offer {
      background: linear-gradient(135deg, rgba(0, 200, 83, .2), rgba(0, 120, 50, .15));
      border: 1px solid rgba(0, 200, 83, .3);
      border-radius: 10px;
      padding: 9px 10px
    }

    .po-tag {
      font-size: .58rem;
      color: #00c853;
      font-weight: 700;
      margin-bottom: 3px
    }

    .po-txt {
      font-size: .6rem;
      color: rgba(255, 255, 255, .75);
      line-height: 1.4
    }

    /* floating notification cards */
    .fn {
      position: absolute;
      background: #fff;
      border-radius: 14px;
      padding: 10px 14px;
      display: flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 8px 28px rgba(0, 0, 0, .14);
      border: 1px solid rgba(0, 168, 67, .12);
      z-index: 3;
      animation: flt 3.5s ease-in-out infinite;
      min-width: 170px
    }

    .fn .fn-ico {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 1rem
    }

    .fn .fn-lbl {
      font-size: .67rem;
      color: var(--text-m);
      font-weight: 500
    }

    .fn .fn-val {
      font-size: .88rem;
      font-weight: 800;
      color: var(--text)
    }

    .fn1 {
      top: -20px;
      left: -130px;
      animation-delay: 0s
    }

    .fn2 {
      bottom: 40px;
      right: -140px;
      animation-delay: 1.2s
    }

    .fn3 {
      bottom: -10px;
      left: -110px;
      animation-delay: 2.1s
    }

    @keyframes flt {

      0%,
      100% {
        transform: translateY(0)
      }

      50% {
        transform: translateY(-9px)
      }
    }

    /* hero pills */
    .hero-pills-row {
      display: flex;
      gap: .65rem;
      flex-wrap: wrap;
      justify-content: center;
      padding: 0 0 1.5rem;
      position: relative;
      z-index: 2
    }

    .hpill {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      background: rgba(255, 255, 255, .15);
      backdrop-filter: blur(6px);
      border: 1px solid rgba(255, 255, 255, .28);
      border-radius: 50px;
      padding: .42rem 1rem;
      font-size: .78rem;
      font-weight: 600;
      color: #fff;
      transition: all .22s
    }

    .hpill:hover,
    .hpill.active {
      background: rgba(255, 255, 255, .28);
      border-color: rgba(255, 255, 255, .5)
    }

    .hpill i {
      font-size: .8rem
    }

    /* ═══════════════════════════════════════════════════════════
     MARQUEE
  ═══════════════════════════════════════════════════════════ */
    .mq-wrap {
      background: #fff;
      border-bottom: 1px solid var(--border);
      padding: .85rem 0;
      overflow: hidden
    }

    .mq-track {
      display: flex;
      gap: 2.8rem;
      animation: mq 26s linear infinite;
      width: max-content
    }

    .mq-item {
      display: flex;
      align-items: center;
      gap: .48rem;
      font-size: .81rem;
      color: var(--text-m);
      white-space: nowrap;
      font-weight: 500
    }

    .mq-item i {
      color: var(--green);
      font-size: .82rem
    }

    @keyframes mq {
      0% {
        transform: translateX(0)
      }

      100% {
        transform: translateX(-50%)
      }
    }

    /* ═══════════════════════════════════════════════════════════
     SERVICES
  ═══════════════════════════════════════════════════════════ */
    .services {
      padding: 5.5rem 0;
      background: #fff;
      overflow: hidden;
      position: relative
    }

    .svc-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
      gap: 1.2rem
    }

    .sc {
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: var(--r);
      padding: 1.6rem 1.2rem;
      text-align: center;
      transition: all .3s;
      position: relative;
      overflow: hidden;
      cursor: default
    }

    .sc::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--grad);
      transform: scaleX(0);
      transition: .3s
    }

    .sc:hover {
      transform: translateY(-5px);
      border-color: rgba(0, 168, 67, .35);
      box-shadow: var(--sh-g)
    }

    .sc:hover::after {
      transform: scaleX(1)
    }

    .sc-ico {
      width: 60px;
      height: 60px;
      background: var(--green-l);
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto .85rem;
      border: 1px solid rgba(0, 168, 67, .18);
      transition: .3s
    }

    .sc:hover .sc-ico {
      background: rgba(0, 168, 67, .18);
      box-shadow: 0 0 16px rgba(0, 168, 67, .2)
    }

    .sc-ico i {
      font-size: 1.5rem;
      color: var(--green)
    }

    .sc h3 {
      font-size: .91rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: .3rem
    }

    .sc p {
      font-size: .74rem;
      color: var(--text-m);
      line-height: 1.5
    }

    .sc-tag {
      display: inline-block;
      background: var(--green-l);
      color: var(--green-d);
      font-size: .6rem;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 50px;
      margin-top: .5rem;
      text-transform: uppercase;
      letter-spacing: .4px
    }

    /* ═══════════════════════════════════════════════════════════
     HOW IT WORKS
  ═══════════════════════════════════════════════════════════ */
    .hiw {
      padding: 5.5rem 0;
      background: var(--alt-bg);
      overflow: hidden;
      position: relative
    }

    .hiw-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 2rem;
      position: relative
    }

    .hiw-grid::before {
      content: '';
      position: absolute;
      top: 34px;
      left: 12.5%;
      right: 12.5%;
      height: 2px;
      background: linear-gradient(90deg, var(--green), var(--green-d));
      z-index: 0
    }

    .hiw-step {
      text-align: center;
      position: relative;
      z-index: 1
    }

    .step-n {
      width: 68px;
      height: 68px;
      background: var(--grad);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      font-family: 'Rajdhani', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: #fff;
      box-shadow: 0 6px 20px rgba(0, 168, 67, .32);
      position: relative
    }

    .step-n::after {
      content: '';
      position: absolute;
      inset: -4px;
      border-radius: 50%;
      border: 2px solid rgba(0, 168, 67, .25)
    }

    .hiw-step h3 {
      font-size: .98rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: .4rem
    }

    .hiw-step p {
      font-size: .79rem;
      color: var(--text-m);
      line-height: 1.6
    }

    /* ═══════════════════════════════════════════════════════════
     STATS
  ═══════════════════════════════════════════════════════════ */
    .stats {
      padding: 4.5rem 0;
      background: var(--grad);
      position: relative;
      overflow: hidden
    }

    .stats::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: linear-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .06) 1px, transparent 1px);
      background-size: 38px 38px
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 2rem;
      position: relative;
      z-index: 1
    }

    .st-item {
      text-align: center
    }

    .st-num {
      font-family: 'Rajdhani', sans-serif;
      font-size: clamp(2.2rem, 4vw, 3rem);
      font-weight: 700;
      color: #fff;
      line-height: 1;
      margin-bottom: .3rem
    }

    .st-lbl {
      font-size: .85rem;
      color: rgba(255, 255, 255, .8);
      font-weight: 500
    }

    .st-bar {
      width: 36px;
      height: 3px;
      background: rgba(255, 255, 255, .45);
      margin: .5rem auto 0;
      border-radius: 2px
    }

    /* ═══════════════════════════════════════════════════════════
     FEATURES
  ═══════════════════════════════════════════════════════════ */
    .features {
      padding: 5.5rem 0;
      background: #fff;
      overflow: hidden;
      position: relative
    }

    .feat-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center
    }

    .feat-img-side {
      position: relative
    }

    .feat-img-box {
      border-radius: var(--rl);
      overflow: hidden;
      box-shadow: 0 12px 40px rgba(0, 100, 40, .14);
      border: 1px solid var(--border)
    }

    .feat-img-box img {
      width: 100%;
      height: 380px;
      object-fit: cover;
      display: block
    }

    .feat-ov {
      position: absolute;
      bottom: 20px;
      left: 20px;
      right: 20px;
      background: rgba(10, 30, 18, .9);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(0, 200, 83, .22);
      border-radius: var(--r);
      padding: .85rem 1.1rem;
      display: flex;
      align-items: center;
      gap: .85rem
    }

    .feat-ov i {
      font-size: 1.7rem;
      color: #00c853;
      flex-shrink: 0
    }

    .feat-ov h4 {
      font-size: .9rem;
      font-weight: 700;
      color: #fff
    }

    .feat-ov p {
      font-size: .72rem;
      color: rgba(255, 255, 255, .62)
    }

    .feat-list {
      display: flex;
      flex-direction: column;
      gap: 1.1rem
    }

    .fi {
      display: flex;
      gap: .9rem;
      align-items: flex-start;
      padding: 1.1rem;
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: var(--r);
      transition: all .3s
    }

    .fi:hover {
      border-color: rgba(0, 168, 67, .32);
      box-shadow: 0 4px 16px rgba(0, 168, 67, .1);
      transform: translateX(4px)
    }

    .fi-ico {
      width: 46px;
      height: 46px;
      background: var(--green-l);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      border: 1px solid rgba(0, 168, 67, .18)
    }

    .fi-ico i {
      color: var(--green);
      font-size: 1.15rem
    }

    .fi h4 {
      font-size: .93rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: .22rem
    }

    .fi p {
      font-size: .78rem;
      color: var(--text-m);
      line-height: 1.6
    }

    /* ═══════════════════════════════════════════════════════════
     OPERATORS
  ═══════════════════════════════════════════════════════════ */
    .operators {
      padding: 4.5rem 0;
      background: var(--alt-bg);
      overflow: hidden;
      position: relative
    }

    .op-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1.2rem;
      flex-wrap: wrap
    }

    .op-chip {
      display: flex;
      align-items: center;
      gap: .6rem;
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: 50px;
      padding: .6rem 1.2rem;
      font-size: .84rem;
      font-weight: 700;
      color: var(--text);
      transition: all .3s;
      box-shadow: var(--sh)
    }

    .op-chip:hover {
      border-color: rgba(0, 168, 67, .38);
      box-shadow: var(--sh-g);
      transform: translateY(-2px)
    }

    .op-dot {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      overflow: hidden
    }

    /* ═══════════════════════════════════════════════════════════
     TESTIMONIALS
  ═══════════════════════════════════════════════════════════ */
    .testi {
      padding: 5.5rem 0;
      background: #fff;
      overflow: hidden;
      position: relative
    }

    .testi-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.4rem
    }

    .tc {
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: var(--rl);
      padding: 1.8rem;
      transition: all .3s
    }

    .tc:hover {
      border-color: rgba(0, 168, 67, .28);
      box-shadow: var(--sh-g);
      transform: translateY(-4px)
    }

    .tc-q {
      font-size: 2.8rem;
      color: var(--green);
      opacity: .25;
      font-family: Georgia, serif;
      line-height: 1;
      margin-bottom: .3rem
    }

    .tc-stars {
      color: #f59e0b;
      font-size: .84rem;
      letter-spacing: 2px;
      margin-bottom: .8rem
    }

    .tc-txt {
      font-size: .85rem;
      color: var(--text-m);
      line-height: 1.75;
      margin-bottom: 1.2rem
    }

    .tc-auth {
      display: flex;
      align-items: center;
      gap: .7rem
    }

    .tc-auth img {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid rgba(0, 168, 67, .25)
    }

    .tc-name {
      font-size: .87rem;
      font-weight: 700;
      color: var(--text)
    }

    .tc-role {
      font-size: .72rem;
      color: var(--text-m)
    }

    /* ═══════════════════════════════════════════════════════════
     SECURITY
  ═══════════════════════════════════════════════════════════ */
    .security {
      padding: 5rem 0;
      background: var(--alt-bg);
      overflow: hidden;
      position: relative
    }

    .sec-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.3rem
    }

    .sec-c {
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: var(--r);
      padding: 1.8rem 1.4rem;
      text-align: center;
      transition: all .3s
    }

    .sec-c:hover {
      border-color: rgba(0, 168, 67, .28);
      transform: translateY(-4px);
      box-shadow: var(--sh-g)
    }

    .sec-ico {
      font-size: 2.1rem;
      color: var(--green);
      margin-bottom: .85rem
    }

    .sec-c h3 {
      font-size: .95rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: .4rem
    }

    .sec-c p {
      font-size: .78rem;
      color: var(--text-m);
      line-height: 1.65
    }

    /* ═══════════════════════════════════════════════════════════
     APP DOWNLOAD
  ═══════════════════════════════════════════════════════════ */
    .appdl {
      padding: 5.5rem 0;
      background: #fff;
      overflow: hidden;
      position: relative
    }

    .app-box {
      background: var(--grad);
      border-radius: var(--rl);
      padding: 3.5rem;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 2.5rem;
      align-items: center;
      position: relative;
      overflow: hidden;
      box-shadow: 0 14px 50px rgba(0, 100, 40, .2)
    }

    .app-box::before {
      content: '';
      position: absolute;
      top: -90px;
      right: -90px;
      width: 360px;
      height: 360px;
      background: radial-gradient(circle, rgba(255, 255, 255, .14) 0%, transparent 68%);
      pointer-events: none
    }

    .app-h {
      font-family: 'Rajdhani', sans-serif;
      font-size: clamp(1.8rem, 3vw, 2.4rem);
      font-weight: 700;
      color: #fff;
      margin-bottom: .65rem
    }

    .app-h span {
      color: #c8ffe0
    }

    .app-p {
      color: rgba(255, 255, 255, .8);
      font-size: .97rem;
      line-height: 1.65;
      margin-bottom: 1.8rem;
      max-width: 440px
    }

    .store-btns {
      display: flex;
      gap: .85rem;
      flex-wrap: wrap
    }

    .sb {
      display: inline-flex;
      align-items: center;
      gap: .65rem;
      background: rgba(255, 255, 255, .12);
      border: 1px solid rgba(255, 255, 255, .25);
      border-radius: 12px;
      padding: .72rem 1.25rem;
      color: #fff;
      transition: all .28s
    }

    .sb:hover {
      background: rgba(255, 255, 255, .22);
      border-color: rgba(255, 255, 255, .42)
    }

    .sb i {
      font-size: 1.65rem
    }

    .sb small {
      display: block;
      font-size: .6rem;
      color: rgba(255, 255, 255, .7);
      text-transform: uppercase;
      letter-spacing: .5px
    }

    .sb strong {
      font-size: .95rem
    }

    .app-meta {
      margin-top: 1.4rem;
      display: flex;
      gap: 1.8rem;
      flex-wrap: wrap
    }

    .am {
      display: flex;
      align-items: center;
      gap: .42rem;
      font-size: .79rem;
      color: rgba(255, 255, 255, .75)
    }

    .am i {
      color: #c8ffe0
    }

    .app-right {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem
    }

    .app-img {
      width: 145px;
      height: 185px;
      border-radius: 18px;
      overflow: hidden;
      border: 2px solid rgba(255, 255, 255, .2);
      box-shadow: 0 10px 32px rgba(0, 0, 0, .2)
    }

    .app-img img {
      width: 100%;
      height: 100%;
      object-fit: cover
    }

    .qr-row {
      display: flex;
      align-items: center;
      gap: .7rem
    }

    .qr-box {
      width: 96px;
      height: 96px;
      background: #fff;
      border-radius: 12px;
      padding: 7px;
      flex-shrink: 0
    }

    .qr-box img {
      width: 100%;
      height: 100%
    }

    .qr-row p {
      font-size: .72rem;
      color: rgba(255, 255, 255, .72);
      line-height: 1.5
    }

    /* ═══════════════════════════════════════════════════════════
     FOOTER
  ═══════════════════════════════════════════════════════════ */
    .footer {
      background: #0c1c12;
      border-top: 1px solid rgba(0, 168, 67, .14);
      padding: 4rem 0 0;
      overflow: hidden
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 2.5rem;
      margin-bottom: 2.8rem
    }

    .fb img {
      height: 54px;
      margin-bottom: .9rem
    }

    .fb p {
      font-size: .82rem;
      color: #7aac8a;
      line-height: 1.72;
      margin-bottom: 1.3rem
    }

    .socials {
      display: flex;
      gap: .65rem
    }

    .soc {
      width: 35px;
      height: 35px;
      border-radius: 9px;
      background: rgba(0, 168, 67, .1);
      border: 1px solid rgba(0, 168, 67, .2);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--green);
      font-size: .86rem;
      transition: all .28s
    }

    .soc:hover {
      background: var(--green);
      color: #fff;
      box-shadow: 0 0 12px rgba(0, 168, 67, .35)
    }

    .fc h4 {
      font-size: .83rem;
      font-weight: 700;
      color: var(--green);
      text-transform: uppercase;
      letter-spacing: .5px;
      margin-bottom: 1rem
    }

    .fc li {
      margin-bottom: .52rem
    }

    .fc li a {
      font-size: .8rem;
      color: #7aac8a;
      transition: color .2s
    }

    .fc li a:hover {
      color: var(--green)
    }

    .fcon {
      display: flex;
      align-items: center;
      gap: .5rem;
      font-size: .8rem;
      color: #7aac8a;
      margin-bottom: .6rem
    }

    .fcon i {
      color: var(--green);
      font-size: .82rem;
      width: 15px
    }

    .foot-bot {
      border-top: 1px solid rgba(0, 168, 67, .1);
      padding: 1.3rem 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: .7rem
    }

    .foot-bot p {
      font-size: .77rem;
      color: #537a63
    }

    .foot-bot span {
      color: var(--green)
    }

    .foot-badges {
      display: flex;
      gap: .55rem;
      flex-wrap: wrap
    }

    .foot-b {
      background: rgba(0, 168, 67, .08);
      border: 1px solid rgba(0, 168, 67, .17);
      border-radius: 6px;
      padding: 3px 9px;
      font-size: .67rem;
      color: #7aac8a;
      font-weight: 600
    }

    /* ═══════════════════════════════════════════════════════════
     SCROLL-TOP
  ═══════════════════════════════════════════════════════════ */
    .stop {
      position: fixed;
      bottom: 26px;
      right: 26px;
      width: 44px;
      height: 44px;
      background: var(--grad);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(0, 168, 67, .36);
      opacity: 0;
      transform: translateY(20px);
      transition: all .3s;
      z-index: 999
    }

    .stop.show {
      opacity: 1;
      transform: translateY(0)
    }

    .stop:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 22px rgba(0, 168, 67, .52)
    }

    .stop i {
      color: #fff;
      font-size: 1rem
    }

    /* section dividers */
    .services::before,
    .hiw::before,
    .stats::before,
    .features::before,
    .operators::before,
    .testi::before,
    .security::before,
    .appdl::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0, 168, 67, .22), transparent)
    }

    /* ═══════════════════════════════════════════════════════════
     RESPONSIVE
  ═══════════════════════════════════════════════════════════ */
    @media(max-width:1024px) {
      .hiw-grid {
        grid-template-columns: repeat(2, 1fr)
      }

      .hiw-grid::before {
        display: none
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr)
      }

      .footer-grid {
        grid-template-columns: 1fr 1fr
      }
    }

    @media(max-width:768px) {

      html,
      body {
        overflow-x: hidden
      }

      .nav-links {
        display: none !important
      }

      .hbg {
        display: flex
      }

      .hero-stripe {
        padding: 3.5rem 0 2.5rem
      }

      .hero-inner {
        grid-template-columns: 1fr;
        text-align: center
      }

      .hero-right {
        display: none
      }

      .hero-desc {
        max-width: 100%
      }

      .hero-btns {
        justify-content: center
      }

      .hero-trust {
        justify-content: center
      }

      .hero-h1 {
        font-size: clamp(2rem, 7vw, 2.7rem)
      }

      .hero-badge {
        margin: 0 auto .9rem
      }

      .svc-grid {
        grid-template-columns: repeat(2, 1fr)
      }

      .hiw-grid {
        grid-template-columns: repeat(2, 1fr)
      }

      .hiw-grid::before {
        display: none
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr)
      }

      .feat-grid {
        grid-template-columns: 1fr
      }

      .feat-img-side {
        display: none
      }

      .testi-grid {
        grid-template-columns: 1fr
      }

      .sec-grid {
        grid-template-columns: 1fr 1fr
      }

      .app-box {
        grid-template-columns: 1fr;
        padding: 2.2rem 1.6rem;
        text-align: center
      }

      .app-right {
        display: none
      }

      .store-btns {
        justify-content: center
      }

      .app-meta {
        justify-content: center
      }

      .footer-grid {
        grid-template-columns: 1fr 1fr;
        gap: 1.8rem
      }

      .foot-bot {
        flex-direction: column;
        text-align: center
      }

      .services,
      .testi,
      .appdl,
      .features {
        padding: 4rem 0
      }

      .hiw,
      .operators,
      .security {
        padding: 3.5rem 0
      }

      .stats {
        padding: 3rem 0
      }

      .fn {
        display: none
      }
    }

    @media(max-width:480px) {
      .svc-grid {
        grid-template-columns: 1fr 1fr
      }

      .hiw-grid {
        grid-template-columns: 1fr
      }

      .stats-grid {
        grid-template-columns: 1fr 1fr
      }

      .sec-grid {
        grid-template-columns: 1fr
      }

      .footer-grid {
        grid-template-columns: 1fr
      }

      .hero-btns {
        flex-direction: column;
        align-items: center
      }

      .btn-solid,
      .btn-line {
        width: 100%;
        justify-content: center
      }

      .ttl {
        font-size: clamp(1.6rem, 6vw, 2rem)
      }

      .hero-pills-row {
        gap: .45rem
      }
    }
</style>

</head>

<body>

    <!-- ── TOP BAR ──────────────────────────────────────────────── -->
    <div class="topbar">
        <i class="fas fa-bolt"></i> Instant Recharge &amp; Bill Payment — 24x7 Available &nbsp;
        <i class="fas fa-shield-alt"></i> 100% Secure &nbsp;
        <i class="fas fa-gift"></i> Exclusive Cashback Offers!
    </div>

<!-- ── NAVBAR ───────────────────────────────────────────────── -->
<nav class="nav" id="nav">
    <div class="container">
        <div class="nav-row">
            <a href="#home" class="nav-logo"><img src="{{ asset('web/assets/images/logo.png') }}" alt="ZivoPay" /></a>
            <ul class="nav-links">
                <li><a href="#services">Services</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#testimonials">Reviews</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="{{ route('user.login') }}" class="nav-cta">Login</a></li>
                <li><a href="{{ route('user.register') }}" class="nav-cta">Register</a></li>
            </ul>
            <div class="hbg" id="hbg" onclick="toggleMenu()" role="button" aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </div>
    </div>
    </div>
</nav>

<!-- ── MOBILE MENU ───────────────────────────────────────────── -->
<div class="mmenu" id="mmenu">
    <div class="mm-head">
        <img src="{{ asset('web/assets/images/logo.png') }}" alt="ZivoPay" />
        <div class="mm-x" onclick="closeMenu()"><i class="fas fa-times"></i></div>
    </div>
    <div class="mm-body">
        <a href="#services" onclick="closeMenu()"><i class="fas fa-th-large"></i> Services</a>
        <a href="#how-it-works" onclick="closeMenu()"><i class="fas fa-list-ol"></i> How It Works</a>
        <a href="#features" onclick="closeMenu()"><i class="fas fa-star"></i> Features</a>
        <a href="#testimonials" onclick="closeMenu()"><i class="fas fa-comment-dots"></i> Reviews</a>
        <a href="#security" onclick="closeMenu()"><i class="fas fa-shield-alt"></i> Security</a>
        <a href="#contact" onclick="closeMenu()"><i class="fas fa-envelope"></i> Contact</a>
    </div>
    <div class="mm-foot">
        <a href="{{ route('user.login') }}"><i class="fas fa-mobile-alt"></i>&nbsp; Login</a>
        <a href="{{ route('user.register') }}"><i class="fas fa-user-plus"></i>&nbsp; Register</a>
    </div>
</div>

<!-- ── HERO ─────────────────────────────────────────────────── -->
<section id="home">
    <!-- Green top section with content -->
    <div class="hero-stripe">
        <div class="container">
            <div class="hero-inner">

                <!-- LEFT -->
                <div data-aos="fade-right" data-aos-duration="700">
                    <div class="hero-badge"><i class="fas fa-bolt"></i> India's Fastest Recharge Platform</div>
                    <h1 class="hero-h1">
                        One App.<br>
                        <span class="hi">All Payments.</span>
                        Zero Hassle.
                    </h1>
                    <p class="hero-desc">
                        From mobile recharge to electricity bills — pay everything instantly with
                        <strong>ZivoPay</strong>. Secure, lightning-fast, packed with exclusive cashback.
                    </p>
                    <div class="hero-btns">
                        <a href="#services" class="btn-solid"><i class="fas fa-th-large"></i> Explore Services</a>
                        <a href="#how-it-works" class="btn-line"><i class="fas fa-play-circle"></i> How It Works</a>
                    </div>
            <div class="hero-trust">
                <div class="tr-item"><i class="fas fa-users"></i> 10M+ Users</div>
                <div class="tr-item"><i class="fas fa-star"></i> 4.9 Rated</div>
                <div class="tr-item"><i class="fas fa-lock"></i> PCI DSS Secured</div>
                <div class="tr-item"><i class="fas fa-rupee-sign"></i> Best Cashback</div>
            </div>
        </div>

        <!-- RIGHT — Phone Mockup -->
        <div class="hero-right" data-aos="fade-left" data-aos-duration="700" data-aos-delay="120">
        
            <!-- Floating notification 1 -->
            <div class="fn fn1">
                <div class="fn-ico" style="background:#e6f9ee"><i class="fas fa-mobile-alt"
                        style="color:#00a844;font-size:1rem"></i></div>
                <div>
                    <div class="fn-lbl">Mobile Recharge</div>
                    <div class="fn-val">Airtel ₹299 ✓</div>
                </div>
            </div>
<!-- Phone UI -->
<div class="hero-phone">
    <div class="ph-notch"></div>
    <div class="ph-header">
        <div class="ph-hi">Welcome back!</div>
        <div class="ph-bal">₹ 1,240.50</div>
        <div class="ph-sub">ZivoPay Wallet Balance</div>
    </div>
            <div class="ph-grid">
                <div class="ph-ic"><i class="fas fa-mobile-alt"></i><span>Mobile</span></div>
                <div class="ph-ic"><i class="fas fa-satellite-dish"></i><span>DTH</span></div>
                <div class="ph-ic"><i class="fas fa-bolt"></i><span>Electric</span></div>
                <div class="ph-ic"><i class="fas fa-credit-card"></i><span>Card</span></div>
                <div class="ph-ic"><i class="fas fa-fire-alt"></i><span>Gas</span></div>
                <div class="ph-ic"><i class="fas fa-wifi"></i><span>Internet</span></div>
                <div class="ph-ic"><i class="fas fa-train"></i><span>Train</span></div>
                <div class="ph-ic"><i class="fas fa-film"></i><span>OTT</span></div>
            </div>
            <div class="ph-offer">
                <div class="po-tag">🎁 Today's Offer</div>
                <div class="po-txt">Jio Recharge ₹239 — Get 12% Cashback!</div>
            </div>
            </div>
<!-- Floating notification 2 -->
<div class="fn fn2">
    <div class="fn-ico" style="background:#fff8e1"><i class="fas fa-bolt" style="color:#f59e0b;font-size:1rem"></i>
    </div>
    <div>
        <div class="fn-lbl">Electricity Bill</div>
        <div class="fn-val">₹1,240 Paid ✓</div>
    </div>
    </div>

            <!-- Floating notification 3 -->
            <div class="fn fn3">
                <div class="fn-ico" style="background:#fce8ff"><i class="fas fa-gift" style="color:#a855f7;font-size:1rem"></i>
                </div>
                <div>
                    <div class="fn-lbl">Cashback Earned</div>
                    <div class="fn-val" style="color:#00a844">+ ₹85.00</div>
                </div>
            </div>

        </div>
        </div>
    </div>

    <!-- Service pills row inside green band -->
    <div class="hero-pills-row container" style="margin-top:2rem">
        <span class="hpill active"><i class="fas fa-mobile-alt"></i> Mobile</span>
        <span class="hpill"><i class="fas fa-satellite-dish"></i> DTH</span>
        <span class="hpill"><i class="fas fa-bolt"></i> Electricity</span>
        <span class="hpill"><i class="fas fa-credit-card"></i> Credit Card</span>
        <span class="hpill"><i class="fas fa-fire-alt"></i> Gas</span>
        <span class="hpill"><i class="fas fa-wifi"></i> Broadband</span>
        <span class="hpill"><i class="fas fa-tint"></i> Water</span>
        <span class="hpill"><i class="fas fa-shield-alt"></i> Insurance</span>
    </div>
    </div>

    <!-- Wave divider -->
    <div style="background:var(--grad);line-height:0;overflow:hidden">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none" style="display:block;width:100%;height:60px">
            <path d="M0,0 C360,60 1080,60 1440,0 L1440,60 L0,60 Z" fill="#f6f8f6" />
        </svg>
    </div>
</section>

<!-- ── MARQUEE ───────────────────────────────────────────────── -->
<div class="mq-wrap">
    <div class="mq-track">
        <div class="mq-item"><i class="fas fa-mobile-alt"></i> Mobile Recharge</div>
        <div class="mq-item"><i class="fas fa-satellite-dish"></i> DTH Recharge</div>
        <div class="mq-item"><i class="fas fa-bolt"></i> Electricity Bill</div>
        <div class="mq-item"><i class="fas fa-credit-card"></i> Credit Card Bill</div>
        <div class="mq-item"><i class="fas fa-fire-alt"></i> Gas Bill</div>
        <div class="mq-item"><i class="fas fa-wifi"></i> Broadband</div>
        <div class="mq-item"><i class="fas fa-tint"></i> Water Bill</div>
        <div class="mq-item"><i class="fas fa-train"></i> Train Ticket</div>
        <div class="mq-item"><i class="fas fa-plane"></i> Flight Booking</div>
        <div class="mq-item"><i class="fas fa-film"></i> OTT Subscription</div>
        <div class="mq-item"><i class="fas fa-gamepad"></i> Gaming Top-Up</div>
        <div class="mq-item"><i class="fas fa-shield-alt"></i> Insurance Premium</div>
        <div class="mq-item"><i class="fas fa-mobile-alt"></i> Mobile Recharge</div>
        <div class="mq-item"><i class="fas fa-satellite-dish"></i> DTH Recharge</div>
        <div class="mq-item"><i class="fas fa-bolt"></i> Electricity Bill</div>
        <div class="mq-item"><i class="fas fa-credit-card"></i> Credit Card Bill</div>
        <div class="mq-item"><i class="fas fa-fire-alt"></i> Gas Bill</div>
        <div class="mq-item"><i class="fas fa-wifi"></i> Broadband</div>
        <div class="mq-item"><i class="fas fa-tint"></i> Water Bill</div>
        <div class="mq-item"><i class="fas fa-train"></i> Train Ticket</div>
        <div class="mq-item"><i class="fas fa-plane"></i> Flight Booking</div>
        <div class="mq-item"><i class="fas fa-film"></i> OTT Subscription</div>
        <div class="mq-item"><i class="fas fa-gamepad"></i> Gaming Top-Up</div>
        <div class="mq-item"><i class="fas fa-shield-alt"></i> Insurance Premium</div>
    </div>
</div>

<!-- ── SERVICES ──────────────────────────────────────────────── -->
<section class="services" id="services">
    <div class="container">
        <div data-aos="fade-up">
            <div style="text-align:center"><span class="chip"><i class="fas fa-th-large"></i> Our Services</span></div>
            <h2 class="ttl">Everything You Need to <span>Pay &amp; Recharge</span></h2>
            <p class="sub">All major services under one roof — fast, simple, and rewarding</p>
        </div>
    <div class="svc-grid">
        <div class="sc" data-aos="fade-up" data-aos-delay="0">
            <div class="sc-ico"><i class="fas fa-mobile-alt"></i></div>
            <h3>Mobile Recharge</h3>
            <p>Prepaid &amp; postpaid for all operators instantly</p><span class="sc-tag">Instant</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="40">
            <div class="sc-ico"><i class="fas fa-satellite-dish"></i></div>
            <h3>DTH Recharge</h3>
            <p>Tata Play, Airtel DTH, Dish TV, Sun Direct &amp; more</p><span class="sc-tag">All Operators</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="80">
            <div class="sc-ico"><i class="fas fa-credit-card"></i></div>
            <h3>Credit Card Bill</h3>
            <p>HDFC, SBI, ICICI, Axis, Kotak &amp; all major banks</p><span class="sc-tag">All Banks</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="120">
            <div class="sc-ico"><i class="fas fa-bolt"></i></div>
            <h3>Electricity Bill</h3>
            <p>All state electricity boards across India</p><span class="sc-tag">Pan India</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="0">
            <div class="sc-ico"><i class="fas fa-fire-alt"></i></div>
            <h3>Gas Bill</h3>
            <p>Indane, HP Gas, Mahanagar Gas &amp; more</p><span class="sc-tag">Fast</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="40">
            <div class="sc-ico"><i class="fas fa-wifi"></i></div>
            <h3>Broadband</h3>
            <p>Jio Fiber, ACT, Airtel Xstream, BSNL &amp; others</p><span class="sc-tag">Popular</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="80">
            <div class="sc-ico"><i class="fas fa-tint"></i></div>
            <h3>Water Bill</h3>
            <p>Municipal water board bills across major cities</p><span class="sc-tag">New</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="120">
            <div class="sc-ico"><i class="fas fa-train"></i></div>
            <h3>Train Booking</h3>
            <p>IRCTC train tickets with seat availability</p><span class="sc-tag">IRCTC</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="0">
            <div class="sc-ico"><i class="fas fa-plane-departure"></i></div>
            <h3>Flight Booking</h3>
            <p>Domestic &amp; international flights at best fares</p><span class="sc-tag">Best Price</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="40">
            <div class="sc-ico"><i class="fas fa-film"></i></div>
            <h3>OTT Subscription</h3>
            <p>Netflix, Prime, Hotstar, Sony LIV &amp; more</p><span class="sc-tag">Trending</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="80">
            <div class="sc-ico"><i class="fas fa-shield-alt"></i></div>
            <h3>Insurance Premium</h3>
            <p>Life, health, vehicle insurance payments</p><span class="sc-tag">Secure</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="120">
            <div class="sc-ico"><i class="fas fa-gamepad"></i></div>
            <h3>Gaming Top-Up</h3>
            <p>BGMI, Free Fire, PUBG, Steam Wallet &amp; more</p><span class="sc-tag">Gamers</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="0">
            <div class="sc-ico"><i class="fas fa-bus"></i></div>
            <h3>Bus Ticket</h3>
            <p>Book bus tickets across India with best offers</p><span class="sc-tag">24x7</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="40">
            <div class="sc-ico"><i class="fas fa-university"></i></div>
            <h3>Loan EMI</h3>
            <p>Home, car, personal loan EMI payment</p><span class="sc-tag">All Banks</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="80">
            <div class="sc-ico"><i class="fas fa-money-check-alt"></i></div>
            <h3>FASTag Recharge</h3>
            <p>Recharge FASTag for toll-free travel</p><span class="sc-tag">NHAI</span>
        </div>
        <div class="sc" data-aos="fade-up" data-aos-delay="120">
            <div class="sc-ico"><i class="fas fa-hand-holding-usd"></i></div>
            <h3>Money Transfer</h3>
            <p>Instant bank transfer, UPI &amp; wallet payments</p><span class="sc-tag">Free</span>
        </div>
        </div>
    </div>
</section>

<!-- ── HOW IT WORKS ──────────────────────────────────────────── -->
<section class="hiw" id="how-it-works">
    <div class="container">
        <div data-aos="fade-up">
            <div style="text-align:center"><span class="chip"><i class="fas fa-list-ol"></i> Simple Process</span></div>
            <h2 class="ttl">Pay in <span>4 Easy Steps</span></h2>
            <p class="sub">Recharge or pay any bill in under 30 seconds</p>
        </div>
    <div class="hiw-grid">
        <div class="hiw-step" data-aos="fade-up" data-aos-delay="0">
            <div class="step-n">1</div>
            <h3>Select Service</h3>
            <p>Choose from Mobile, DTH, Bills or any other service you need</p>
        </div>
        <div class="hiw-step" data-aos="fade-up" data-aos-delay="90">
            <div class="step-n">2</div>
            <h3>Enter Details</h3>
            <p>Enter your mobile number, consumer ID or account number</p>
        </div>
        <div class="hiw-step" data-aos="fade-up" data-aos-delay="180">
            <div class="step-n">3</div>
            <h3>Choose &amp; Pay</h3>
            <p>Select plan or enter amount, pay via UPI, card or wallet</p>
        </div>
        <div class="hiw-step" data-aos="fade-up" data-aos-delay="270">
            <div class="step-n">4</div>
            <h3>Instant Confirmation</h3>
            <p>Get instant confirmation &amp; receipt on your phone and email</p>
        </div>
        </div>
    </div>
</section>

<!-- ── STATS ─────────────────────────────────────────────────── -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="st-item" data-aos="fade-up" data-aos-delay="0">
                <div class="st-num">10M+</div>
                <div class="st-lbl">Happy Customers</div>
                <div class="st-bar"></div>
            </div>
        <div class="st-item" data-aos="fade-up" data-aos-delay="80">
            <div class="st-num">500Cr+</div>
            <div class="st-lbl">Transactions Processed</div>
            <div class="st-bar"></div>
        </div>
        <div class="st-item" data-aos="fade-up" data-aos-delay="160">
            <div class="st-num">99.9%</div>
            <div class="st-lbl">Uptime Guaranteed</div>
            <div class="st-bar"></div>
        </div>
        <div class="st-item" data-aos="fade-up" data-aos-delay="240">
            <div class="st-num">50+</div>
            <div class="st-lbl">Services Available</div>
            <div class="st-bar"></div>
        </div>
    </div>
    </div>
    </section>

<!-- ── FEATURES ──────────────────────────────────────────────── -->
<section class="features" id="features">
    <div class="container">
        <div data-aos="fade-up">
            <div style="text-align:center"><span class="chip"><i class="fas fa-star"></i> Why ZivoPay</span></div>
            <h2 class="ttl">Built for <span>Speed, Security &amp; Savings</span></h2>
            <p class="sub">Everything designed to make your payment experience effortless</p>
        </div>
        <div class="feat-grid">
            <div class="feat-img-side" data-aos="fade-right">
                <div class="feat-img-box">
                    <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=600&q=80"
                        alt="Secure Payments" />
                </div>
        <div class="feat-ov">
            <i class="fas fa-shield-alt"></i>
            <div>
                <h4>Bank-Grade Security</h4>
                <p>256-bit SSL Encryption on every transaction</p>
            </div>
        </div>
        </div>
        <div class="feat-list" data-aos="fade-left">
            <div class="fi">
                <div class="fi-ico"><i class="fas fa-bolt"></i></div>
            <div>
            <h4>Lightning Fast Transactions</h4>
            <p>Most recharges and bill payments processed within seconds — even during peak hours.</p>
            </div>
        </div>
        <div class="fi">
            <div class="fi-ico"><i class="fas fa-percent"></i></div>
            <div>
                <h4>Best Cashback &amp; Offers</h4>
                <p>Exclusive daily deals, cashback offers and discount coupons on every payment you make.</p>
            </div>
        </div>
        <div class="fi">
            <div class="fi-ico"><i class="fas fa-bell"></i></div>
            <div>
            <h4>Smart Bill Reminders</h4>
            <p>Never miss a due date — get automated SMS and app reminders before bills expire.</p>
            </div>
        </div>
        <div class="fi">
            <div class="fi-ico"><i class="fas fa-history"></i></div>
            <div>
                <h4>Full Payment History</h4>
                <p>Access complete transaction history with downloadable receipts anytime, anywhere.</p>
            </div>
        </div>
        <div class="fi">
            <div class="fi-ico"><i class="fas fa-headset"></i></div>
            <div>
                <h4>24x7 Customer Support</h4>
                <p>Round-the-clock support via chat, call or email. We're always here to help you.</p>
            </div>
            </div>
            </div>
            </div>
    </div>
    </section>

<!-- ── OPERATORS ─────────────────────────────────────────────── -->
<section class="operators">
    <div class="container">
        <div data-aos="fade-up">
            <h2 class="ttl" style="margin-bottom:.45rem">Trusted by <span>All Major Operators</span></h2>
            <p class="sub">We support 200+ operators and service providers across India</p>
        </div>
    <div class="op-row" data-aos="fade-up" data-aos-delay="80">
        <div class="op-chip">
            <div class="op-dot" style="background:#e40000"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#e40000" /><text x="50%" y="58%" text-anchor="middle"
                        font-size="22" font-weight="bold" fill="#fff" font-family="Arial">A</text>
                </svg></div>Airtel
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#0066cc"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#0066cc" /><text x="50%" y="58%" text-anchor="middle" font-size="19"
                        font-weight="bold" fill="#fff" font-family="Arial">Jio</text>
                </svg></div>Jio
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#cc0000"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#cc0000" /><text x="50%" y="58%" text-anchor="middle" font-size="19"
                        font-weight="bold" fill="#fff" font-family="Arial">Vi</text>
                </svg></div>Vi
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#003087"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#003087" /><text x="50%" y="60%" text-anchor="middle" font-size="13"
                        font-weight="bold" fill="#fff" font-family="Arial">BSNL</text>
                </svg></div>BSNL
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#6d2077"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#6d2077" /><text x="50%" y="60%" text-anchor="middle" font-size="12"
                        font-weight="bold" fill="#fff" font-family="Arial">TATA</text>
                </svg></div>Tata Play
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#d4161c"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#d4161c" /><text x="50%" y="60%" text-anchor="middle" font-size="12"
                        font-weight="bold" fill="#fff" font-family="Arial">DISH</text>
                </svg></div>Dish TV
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#ff6600"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#ff6600" /><text x="50%" y="60%" text-anchor="middle" font-size="12"
                        font-weight="bold" fill="#fff" font-family="Arial">SUN</text>
                </svg></div>Sun Direct
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#004c97"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#004c97" /><text x="50%" y="60%" text-anchor="middle" font-size="12"
                        font-weight="bold" fill="#fff" font-family="Arial">HDFC</text>
                </svg></div>HDFC
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#003399"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#003399" /><text x="50%" y="60%" text-anchor="middle" font-size="13"
                        font-weight="bold" fill="#fff" font-family="Arial">SBI</text>
                </svg></div>SBI Card
        </div>
        <div class="op-chip">
            <div class="op-dot" style="background:#f58220"><svg width="28" height="28" viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="30" fill="#f58220" /><text x="50%" y="60%" text-anchor="middle" font-size="11"
                        font-weight="bold" fill="#fff" font-family="Arial">ICICI</text>
                </svg></div>ICICI
        </div>
        </div>
        </div>
        </section>

<!-- ── TESTIMONIALS ──────────────────────────────────────────── -->
<section class="testi" id="testimonials">
    <div class="container">
        <div data-aos="fade-up">
            <div style="text-align:center"><span class="chip"><i class="fas fa-comment-dots"></i> Customer
                    Reviews</span>
            </div>
            <h2 class="ttl">What Our <span>Users Say</span></h2>
            <p class="sub">Over 10 million happy customers trust ZivoPay every day</p>
            </div>
        <div class="testi-grid">
            <div class="tc" data-aos="fade-up" data-aos-delay="0">
                <div class="tc-q">"</div>
                <div class="tc-stars">★★★★★</div>
                <p class="tc-txt">I've been using ZivoPay for 2 years. Electricity bill, mobile recharge, DTH —
                    everything
                    done in seconds. Cashback offers are amazing!</p>
                <div class="tc-auth"><img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Rajesh" />
                    <div>
                        <div class="tc-name">Rajesh Kumar</div>
                        <div class="tc-role">Business Owner, Delhi</div>
                        </div>
                        </div>
        </div>
        <div class="tc" data-aos="fade-up" data-aos-delay="90">
            <div class="tc-q">"</div>
            <div class="tc-stars">★★★★★</div>
            <p class="tc-txt">Best recharge app ever. Credit card bill payment is seamless and I always get great
                cashback. Super fast and reliable!</p>
            <div class="tc-auth"><img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Priya" />
            <div>
            <div class="tc-name">Priya Sharma</div>
            <div class="tc-role">Software Engineer, Bengaluru</div>
            </div>
            </div>
        </div>
        <div class="tc" data-aos="fade-up" data-aos-delay="180">
            <div class="tc-q">"</div>
            <div class="tc-stars">★★★★★</div>
            <p class="tc-txt">ZivoPay saved me so much time. All house bills — gas, water, internet — managed from one
                place. Customer support is very quick!</p>
            <div class="tc-auth"><img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Amit" />
            <div>
            <div class="tc-name">Amit Patel</div>
            <div class="tc-role">CA, Ahmedabad</div>
            </div>
            </div>
        </div>
        <div class="tc" data-aos="fade-up" data-aos-delay="0">
            <div class="tc-q">"</div>
            <div class="tc-stars">★★★★★</div>
            <p class="tc-txt">I recharge Jio and pay electricity bills every month through ZivoPay. Never had a failed
                transaction. Cashback is credited instantly!</p>
            <div class="tc-auth"><img src="https://randomuser.me/api/portraits/women/28.jpg" alt="Sneha" />
                <div>
                    <div class="tc-name">Sneha Verma</div>
                    <div class="tc-role">Teacher, Mumbai</div>
            </div>
            </div>
            </div>
        <div class="tc" data-aos="fade-up" data-aos-delay="90">
            <div class="tc-q">"</div>
            <div class="tc-stars">★★★★★</div>
            <p class="tc-txt">FASTag and insurance premium through ZivoPay is super convenient. Offers are unbeatable and
                transactions always instant!</p>
            <div class="tc-auth"><img src="https://randomuser.me/api/portraits/men/55.jpg" alt="Suresh" />
                <div>
                    <div class="tc-name">Suresh Nair</div>
                    <div class="tc-role">Entrepreneur, Chennai</div>
            </div>
            </div>
        </div>
        <div class="tc" data-aos="fade-up" data-aos-delay="180">
            <div class="tc-q">"</div>
            <div class="tc-stars">★★★★★</div>
            <p class="tc-txt">Excellent app with clean interface. DTH and broadband bill payments take less than 10
                seconds. Highly recommended!</p>
            <div class="tc-auth"><img src="https://randomuser.me/api/portraits/women/33.jpg" alt="Kavya" />
                <div>
                    <div class="tc-name">Kavya Reddy</div>
                    <div class="tc-role">Doctor, Hyderabad</div>
            </div>
        </div>
        </div>
    </div>
    </div>
</section>

<!-- ── SECURITY ───────────────────────────────────────────────── -->
<section class="security" id="security">
    <div class="container">
        <div data-aos="fade-up">
            <div style="text-align:center"><span class="chip"><i class="fas fa-shield-alt"></i> 100% Safe</span></div>
            <h2 class="ttl">Your Money is <span>Always Protected</span></h2>
            <p class="sub">Military-grade security protocols to keep every rupee safe</p>
        </div>
    <div class="sec-grid">
        <div class="sec-c" data-aos="fade-up" data-aos-delay="0">
            <div class="sec-ico"><i class="fas fa-lock"></i></div>
            <h3>256-bit SSL Encryption</h3>
            <p>All data encrypted with bank-level SSL security ensuring your information is always safe.</p>
        </div>
        <div class="sec-c" data-aos="fade-up" data-aos-delay="80">
            <div class="sec-ico"><i class="fas fa-fingerprint"></i></div>
            <h3>2-Factor Authentication</h3>
            <p>Extra layer of protection with OTP and biometric verification for every transaction.</p>
        </div>
        <div class="sec-c" data-aos="fade-up" data-aos-delay="160">
            <div class="sec-ico"><i class="fas fa-shield-alt"></i></div>
            <h3>PCI DSS Compliant</h3>
            <p>Fully compliant with international Payment Card Industry Data Security Standards.</p>
        </div>
        <div class="sec-c" data-aos="fade-up" data-aos-delay="0">
            <div class="sec-ico"><i class="fas fa-eye-slash"></i></div>
            <h3>Data Privacy</h3>
            <p>We never share your personal data with third parties. Your privacy is our top priority.</p>
        </div>
        <div class="sec-c" data-aos="fade-up" data-aos-delay="80">
            <div class="sec-ico"><i class="fas fa-undo-alt"></i></div>
            <h3>Instant Refund Policy</h3>
            <p>Failed transaction? Get instant refunds directly to your original payment method.</p>
        </div>
        <div class="sec-c" data-aos="fade-up" data-aos-delay="160">
            <div class="sec-ico"><i class="fas fa-certificate"></i></div>
            <h3>RBI Regulated</h3>
            <p>ZivoPay is fully regulated and licensed by the Reserve Bank of India.</p>
        </div>
    </div>
    </div>
</section>

<!-- ── APP DOWNLOAD ───────────────────────────────────────────── -->
<section class="appdl" id="download">
    <div class="container">
        <div class="app-box" data-aos="fade-up">
            <div class="app-left" style="position:relative;z-index:1">
                <div class="chip" style="background:rgba(255,255,255,.2);color:#fff;border-color:rgba(255,255,255,.3)">
                    <i class="fas fa-mobile-alt"></i>&nbsp;Download Now
                </div>
                <h2 class="app-h">Get the <span>ZivoPay App</span><br>and Pay Smarter</h2>
                <p class="app-p">Available on Android &amp; iOS. Experience the fastest, most secure recharge and bill
                    payment
                    app in India. Join 10M+ happy users today.</p>
                <div class="store-btns">
                    <a href="#" class="sb"><i class="fab fa-google-play"></i>
                        <div><small>Get it on</small><strong>Google Play</strong></div>
                    </a>
                    <a href="#" class="sb"><i class="fab fa-apple"></i>
                        <div><small>Download on</small><strong>App Store</strong></div>
                    </a>
                </div>
        <div class="app-meta">
            <div class="am"><i class="fas fa-star"></i> <strong style="color:#fff">4.9</strong>&nbsp;Play Store</div>
            <div class="am"><i class="fas fa-download"></i> <strong style="color:#fff">10M+</strong>&nbsp;Downloads
            </div>
            <div class="am"><i class="fas fa-shield-alt"></i> 100% Secure</div>
            </div>
            </div>
        <div class="app-right">
            <div class="app-img"><img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=300&q=85"
                    alt="ZivoPay App" /></div>
            <div class="qr-row">
                <div class="qr-box"><img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=https://zivopay.com&color=007a30&bgcolor=ffffff"
                        alt="QR" /></div>
                <p>Scan to<br>Download<br>ZivoPay App</p>
            </div>
        </div>
    </div>
    </div>
    </section>

<!-- ── FOOTER ─────────────────────────────────────────────────── -->
<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-grid">
            <div class="fb">
                <img src="{{ asset('web/assets/images/logo.png') }}" alt="ZivoPay" />
                <p>India's most trusted recharge &amp; bill payment platform. Pay smarter, save more — Evening. One Zivo
                    Pay.
                </p>
                <div class="socials">
                    <a href="#" class="soc" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="soc" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="soc" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="soc" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="soc" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
                </div>
                <div class="fc">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#testimonials">Reviews</a></li>
                        <li><a href="#download">Download App</a></li>
                    </ul>
                </div>
                <div class="fc">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Mobile Recharge</a></li>
                        <li><a href="#">DTH Recharge</a></li>
                        <li><a href="#">Electricity Bill</a></li>
                        <li><a href="#">Credit Card Bill</a></li>
                        <li><a href="#">Gas Bill</a></li>
                        <li><a href="#">Broadband</a></li>
                        <li><a href="#">Insurance</a></li>
                        <li><a href="#">Money Transfer</a></li>
                    </ul>
        </div>
        <div class="fc">
            <h4>Contact Us</h4>
            <div class="fcon"><i class="fas fa-envelope"></i>support@zivopay.com</div>
            <div class="fcon"><i class="fas fa-phone"></i>1800-XXX-XXXX (Toll Free)</div>
            <div class="fcon"><i class="fas fa-clock"></i>24x7 Support</div>
            <div class="fcon"><i class="fas fa-map-marker-alt"></i>India</div>
            <br>
            <ul>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms &amp; Conditions</a></li>
                <li><a href="#">Refund Policy</a></li>
                <li><a href="#">Grievance Redressal</a></li>
            </ul>
        </div>
        </div>
    <div class="foot-bot">
        <p>© 2026 <span>ZivoPay</span>. All Rights Reserved. | RBI Licensed Payment Aggregator</p>
        <div class="foot-badges">
            <span class="foot-b"><i class="fas fa-lock"></i> SSL Secured</span>
            <span class="foot-b">PCI DSS</span>
            <span class="foot-b">RBI Licensed</span>
            <span class="foot-b">ISO 27001</span>
        </div>
    </div>
    </div>
</footer>

<!-- ── SCROLL TOP ─────────────────────────────────────────────── -->
<div class="stop" id="stop" onclick="window.scrollTo({top:0,behavior:'smooth'})" role="button" aria-label="Back to top">
    <i class="fas fa-arrow-up"></i>
</div>

<!-- ── SCRIPTS ────────────────────────────────────────────────── -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({ once: true, offset: 60, duration: 680 });

    // Navbar scroll
    const nav = document.getElementById('nav');
    const stop = document.getElementById('stop');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 60);
        stop.classList.toggle('show', window.scrollY > 400);
    });

                    // Mobile menu
                    const hbg = document.getElementById('hbg');
                    const mmenu = document.getElementById('mmenu');

                    function toggleMenu() {
                        const open = mmenu.classList.toggle('on');
                        hbg.classList.toggle('on', open);
                        hbg.setAttribute('aria-expanded', open);
                        document.documentElement.style.overflow = open ? 'hidden' : '';
                        document.body.style.overflow = open ? 'hidden' : '';
                    }

                    function closeMenu() {
                        mmenu.classList.remove('on');
                        hbg.classList.remove('on');
                        hbg.setAttribute('aria-expanded', 'false');
                        document.documentElement.style.overflow = '';
                        document.body.style.overflow = '';
                    }

                    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
              </script>
</body>

</html>
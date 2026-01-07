<?php
session_start();
require_once "data_produk.php";

// Logika Pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filtered_products = [];

if ($search) {
    $search_lower = strtolower($search);
    foreach ($products as $p) {
        if (strpos(strtolower($p['name']), $search_lower) !== false || strpos(strtolower($p['desc']), $search_lower) !== false) {
            $filtered_products[] = $p;
        }
    }
} else {
    $filtered_products = $products;
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberSoft Store - Software Masa Depan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        .btn-hero-cta {
            display: inline-block;
            margin-top: 2rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'JetBrains Mono', monospace;
            background: #0f0f23;
            color: #e2e8f0;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Background Image dengan Animasi & Transparansi */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('asset/background.jpg') no-repeat center center/cover;
            opacity: 0.4; /* Mengatur transparansi background */
            z-index: -2;
            filter: blur(3px);
            animation: bgZoom 30s infinite alternate;
        }

        @keyframes bgZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.3);
            padding: 1rem 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #50fa7b;
            text-decoration: none;
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .hamburger {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            gap: 4px;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            margin-left: 15px;
            position: relative;
        }

        .hamburger:hover {
            background: rgba(80, 250, 123, 0.1);
            transform: scale(1.1);
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #e2e8f0;
            transition: all 0.3s ease;
            transform-origin: center;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        .nav-links {
            display: flex;
            position: absolute;
            top: 100%;
            right: 20px;
            width: 250px;
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            padding: 1rem 0;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-links.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem;
            text-align: left;
            border-radius: 8px;
        }

        .nav-links a:hover {
            color: #50fa7b;
            background: rgba(80, 250, 123, 0.1);
        }

        .btn-hero-cta {
            display: inline-block;
            margin-top: 2rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .btn-hero-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(80, 250, 123, 0.4);
        }

        .btn-login {
            background: linear-gradient(135deg, #bd93f9, #ff79c6);
            color: #0f0f23 !important;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(189, 147, 249, 0.3);
        }

        .cart-icon {
            position: relative;
            font-size: 1.5rem;
            color: #e2e8f0;
            text-decoration: none;
            transition: color 0.3s ease;
            margin-left: auto;
        }

        .cart-icon:hover {
            color: #50fa7b;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff5555;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hero Section */
        .hero {
            background: transparent;
            padding: 120px 20px 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(80, 250, 123, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 184, 108, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #50fa7b, #ffb86c, #bd93f9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease-out;
        }

        .hero p {
            font-size: 1.2rem;
            color: #a0a0b0;
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px;
        }

        .section-title {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 3rem;
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }

        /* Search */
        .search-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .search-input {
            padding: 1rem 1.5rem;
            border: 2px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px 0 0 12px;
            background: rgba(26, 26, 46, 0.6);
            color: #e2e8f0;
            font-size: 1rem;
            width: 300px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #50fa7b;
            box-shadow: 0 0 20px rgba(80, 250, 123, 0.2);
        }

        .search-btn {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            border: none;
            border-radius: 0 12px 12px 0;
            color: #0f0f23;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(80, 250, 123, 0.3);
        }

        /* Product Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .card {
            background: rgba(30, 30, 46, 0.6); /* Glassmorphism */
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .card-image {
            width: 100%;
            height: 180px;
            background: #2a2a3e;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .card:hover .card-image img {
            transform: scale(1.1);
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #50fa7b, #ffb86c, #bd93f9);
            border-radius: 16px 16px 0 0;
            transition: height 0.3s ease;
        }

        .card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4), 0 0 30px rgba(80, 250, 123, 0.1);
            border-color: rgba(80, 250, 123, 0.5);
        }

        .card:hover::before {
            height: 6px;
        }

        .card:hover .btn-buy {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(80, 250, 123, 0.4);
        }

        .card h3 {
            color: #f8f8f2;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #50fa7b;
            margin-bottom: 1rem;
        }

        .features {
            list-style: none;
            margin-bottom: 1.5rem;
        }

        .features li {
            color: #a0a0b0;
            margin-bottom: 0.5rem;
            position: relative;
            padding-left: 1.5rem;
        }

        .features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #50fa7b;
            font-weight: bold;
        }

        .btn-buy {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .btn-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(80, 250, 123, 0.6);
        }

        /* Efek Kilau pada Button */
        .btn-buy::after {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: 0.5s;
        }
        .btn-buy:hover::after {
            left: 100%;
        }

        /* About Section */
        .about-us-section {
            background: rgba(26, 26, 46, 0.8);
            backdrop-filter: blur(5px);
            padding: 80px 0;
            text-align: center;
        }

        .about-us-section p {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
            color: #a0a0b0;
            line-height: 1.8;
        }

        /* About Section Gallery */
        .about-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 3rem;
            align-items: center;
        }

        .about-gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .gallery-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1;
        }

        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover .gallery-img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 1rem;
            text-align: center;
            font-weight: 600;
        }

        /* Testimonials */
        .testimonials-section {
            padding: 80px 0;
            background: transparent;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .testimonial-card {
            background: rgba(30, 30, 46, 0.6);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .testimonial-text {
            font-style: italic;
            color: #e2e8f0;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .testimonial-author {
            color: #50fa7b;
            font-weight: 600;
            text-align: right;
        }

        /* FAQ Section */
        .faq-section {
            padding: 80px 0;
            background: rgba(26, 26, 46, 0.8);
        }

        .faq-container {
            max-width: 800px;
        }

        details {
            background: rgba(30, 30, 46, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            margin-bottom: 1rem;
            border: 1px solid rgba(99, 102, 241, 0.2);
            overflow: hidden;
        }

        summary {
            padding: 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: #f8f8f2;
            transition: background 0.3s ease;
        }

        summary:hover {
            background: rgba(80, 250, 123, 0.1);
        }

        .faq-answer {
            padding: 0 1.5rem 1.5rem;
            color: #a0a0b0;
            line-height: 1.6;
        }

        /* Contact Section */
        .contact-section {
            padding: 80px 0;
            background: transparent;
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #bd93f9;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            background: rgba(26, 26, 46, 0.6);
            border: 2px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: #e2e8f0;
            font-family: inherit;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #50fa7b;
            box-shadow: 0 0 20px rgba(80, 250, 123, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            border: none;
            border-radius: 8px;
            color: #0f0f23;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(80, 250, 123, 0.3);
        }

        /* Footer */
        .footer {
            background: rgba(15, 15, 35, 0.9);
            backdrop-filter: blur(10px);
            padding: 40px 20px;
            text-align: center;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
        }

        .social-links {
            margin-bottom: 1rem;
        }

        .social-links a {
            color: #a0a0b0;
            text-decoration: none;
            margin: 0 1rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: #50fa7b;
        }

        .footer p {
            color: #6272a4;
            font-size: 0.9rem;
        }

        /* Back to Top Button */
        #backToTop {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        #backToTop.show {
            opacity: 1;
            visibility: visible;
        }

        #backToTop:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(80, 250, 123, 0.3);
        }

        /* Chat Widget */
        .chat-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: linear-gradient(135deg, #bd93f9, #ff79c6);
            color: #0f0f23;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(189, 147, 249, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .chat-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(189, 147, 249, 0.4);
        }

        .chat-window {
            position: fixed;
            bottom: 90px;
            left: 20px;
            width: 350px;
            height: 400px;
            background: rgba(30, 30, 46, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            display: none;
            flex-direction: column;
            z-index: 1001;
        }

        .chat-header {
            background: linear-gradient(135deg, #bd93f9, #ff79c6);
            color: #0f0f23;
            padding: 1rem;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }

        .chat-close {
            cursor: pointer;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .chat-close:hover {
            transform: scale(1.2);
        }

        .chat-messages {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            background: rgba(15, 15, 35, 0.5);
        }

        .message {
            margin-bottom: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .message.received {
            background: rgba(80, 250, 123, 0.2);
            color: #50fa7b;
            align-self: flex-start;
        }

        .message.sent {
            background: rgba(189, 147, 249, 0.2);
            color: #bd93f9;
            align-self: flex-end;
            margin-left: auto;
        }

        .chat-input-area {
            display: flex;
            padding: 1rem;
            background: rgba(26, 26, 46, 0.8);
            border-radius: 0 0 16px 16px;
        }

        .chat-input {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            background: rgba(15, 15, 35, 0.8);
            color: #e2e8f0;
            outline: none;
        }

        .chat-send {
            margin-left: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .chat-send:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(80, 250, 123, 0.3);
        }

        /* Cookie Banner */
        .cookie-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(30, 30, 46, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(99, 102, 241, 0.2);
            padding: 1rem;
            display: none;
            justify-content: space-between;
            align-items: center;
            z-index: 1002;
        }

        .cookie-text {
            color: #a0a0b0;
            font-size: 0.9rem;
        }

        .cookie-btn {
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cookie-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(80, 250, 123, 0.3);
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Scroll Animation Classes */
        .scroll-animate {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.6s ease-out;
        }

        .scroll-animate.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .slide-left {
            opacity: 0;
            transform: translateX(-50px);
            transition: all 0.6s ease-out;
        }

        .slide-left.animate {
            opacity: 1;
            transform: translateX(0);
        }

        .slide-right {
            opacity: 0;
            transform: translateX(50px);
            transition: all 0.6s ease-out;
        }

        .slide-right.animate {
            opacity: 1;
            transform: translateX(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .testimonial-grid {
                grid-template-columns: 1fr;
            }

            .chat-window {
                width: calc(100vw - 40px);
                left: 20px;
                right: 20px;
            }

            .search-input {
                width: 200px;
            }
        }

        .navbar-right {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container" style="position: relative;">
            <a href="index.php" class="logo">CyberSoft</a>
            <div class="navbar-right">
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="logout.php" class="btn-login" style="background: linear-gradient(135deg, #ff5555, #ff79c6);">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>
                
                <div class="hamburger" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="nav-links">
                <a href="#products">Produk</a>
                <a href="#about">Tentang Kami</a>
                <a href="#faq">FAQ</a>
                <a href="#contact">Kontak</a>
                <?php if (isset($_SESSION['username'])): ?>
                    <?php if ($_SESSION['level'] == 'admin'): ?>
                        <a href="dashboard.php">Dashboard</a>
                    <?php else: ?>
                        <a href="dashboardCustomer.php">Akun Saya</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="hero">
        <h1>Upgrade Digital Life Anda</h1>
        <p>Solusi software terbaik untuk produktivitas, keamanan, dan kreativitas tanpa batas.</p>
        <a href="#products" class="btn-hero-cta">Shop Now</a>
    </header>

    <div class="container" id="products">
        <h2 class="section-title">Pilihan Software Unggulan</h2>
        
        <div class="search-wrapper">
            <form action="" method="GET">
                <input type="text" name="search" class="search-input" placeholder="Cari software..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="search-btn">Cari</button>
            </form>
        </div>

        <div class="grid">
            <?php if (empty($filtered_products)): ?>
                <p style="text-align:center; grid-column: 1/-1; color: #ffb86c;">Produk tidak ditemukan.</p>
            <?php else: ?>
                <?php foreach ($filtered_products as $p): ?>
                    <div class="card">
                        <div class="card-image">
                            <img src="<?= isset($p['image']) ? $p['image'] : 'asset/default.jpg' ?>" alt="<?= htmlspecialchars($p['name']) ?>" onerror="this.src='https://via.placeholder.com/300x200?text=<?= urlencode($p['name']) ?>'">
                        </div>
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <p style="color: #a0a0b0;"><?= htmlspecialchars($p['desc']) ?></p>
                        <ul class="features">
                            <?php foreach ($p['features'] as $f): ?>
                                <li><?= htmlspecialchars($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="detail_produk.php?name=<?= urlencode($p['name']) ?>" class="btn-buy">Lihat Detail</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <section class="about-us-section" id="about">
        <div class="container">
            <h2 class="section-title">Tentang CyberSoft</h2>
            <div class="about-content">
                <div class="about-text scroll-animate">
                    <p>
                        CyberSoft didirikan pada tahun 2020 dengan misi untuk menyediakan solusi perangkat lunak yang inovatif, aman, dan andal bagi individu maupun bisnis. Kami percaya bahwa teknologi yang tepat dapat membuka potensi tak terbatas. Tim kami terdiri dari para ahli yang bersemangat dalam menciptakan produk yang tidak hanya fungsional tetapi juga mudah digunakan. Dari keamanan siber hingga alat kreativitas, kami berkomitmen untuk memberikan yang terbaik bagi pelanggan kami di seluruh dunia.
                    </p>
                </div>
                <div class="about-gallery">
                    <div class="gallery-item slide-left">
                        <img src="asset/technology-digital-electronic-product.webp" alt="Technology Products" class="gallery-img">
                        <div class="gallery-overlay">
                            <span>Produk Digital</span>
                        </div>
                    </div>
                    <div class="gallery-item slide-right">
                        <img src="asset/website for mobile store.jpg" alt="Mobile Store" class="gallery-img">
                        <div class="gallery-overlay">
                            <span>Store Mobile</span>
                        </div>
                    </div>
                    <div class="gallery-item scroll-animate">
                        <img src="asset/software-background.jpg" alt="Software Background" class="gallery-img">
                        <div class="gallery-overlay">
                            <span>Background Software</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title">Apa Kata Mereka?</h2>
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text">"Antivirus Pro 2025 benar-benar menyelamatkan data perusahaan kami dari serangan ransomware. Sangat direkomendasikan!"</p>
                    <div class="testimonial-author">- Budi Santoso, CEO TechIndo</div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"Video Editor X sangat ringan namun fiturnya setara dengan software editing profesional mahal lainnya. Rendering 4K super cepat!"</p>
                    <div class="testimonial-author">- Sarah Wijaya, Content Creator</div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"Cloud ERP System membantu saya mengelola keuangan toko cabang dengan sangat mudah dari mana saja."</p>
                    <div class="testimonial-author">- Andi Pratama, Pemilik Retail</div>
                </div>
            </div>
        </div>
    </section>

    <section class="faq-section" id="faq">
        <div class="container faq-container">
            <h2 class="section-title">Pertanyaan Umum (FAQ)</h2>
            
            <details>
                <summary>Bagaimana cara membeli software?</summary>
                <div class="faq-answer">
                    Pilih produk, klik 'Lihat Detail', lalu 'Beli Sekarang' untuk menambahkannya ke keranjang. Setelah checkout, pesanan Anda akan muncul di dashboard akun Anda. Lakukan pembayaran sesuai instruksi dan upload bukti transfer pada halaman riwayat pesanan.
                </div>
            </details>

            <details>
                <summary>Apakah lisensi berlaku selamanya?</summary>
                <div class="faq-answer">
                    Setiap produk memiliki tipe lisensi yang berbeda (seumur hidup atau langganan tahunan). Informasi detail mengenai lisensi dapat ditemukan pada halaman deskripsi masing-masing produk.
                </div>
            </details>

            <details>
                <summary>Metode pembayaran apa saja yang tersedia?</summary>
                <div class="faq-answer">
                    Saat ini kami menerima pembayaran melalui Transfer Bank (BCA & Mandiri) dan E-Wallet (OVO & GoPay). Instruksi detail akan diberikan saat Anda melakukan proses pembayaran di dashboard Anda.
                </div>
            </details>

            <details>
                <summary>Apakah ada garansi uang kembali?</summary>
                <div class="faq-answer">
                    Ya, kami memberikan garansi uang kembali 30 hari jika software tidak berfungsi sesuai dengan spesifikasi yang dijanjikan.
                </div>
            </details>

            <details>
                <summary>Bagaimana jika saya mengalami kendala teknis?</summary>
                <div class="faq-answer">
                    Tim support kami siap membantu 24/7. Anda bisa menghubungi kami melalui halaman Kontak atau fitur live chat jika tersedia.
                </div>
            </details>
        </div>
    </section>

    <section class="contact-section" id="contact">
        <div class="container">
            <h2 class="section-title">Hubungi Kami</h2>
            <div class="contact-form">
                <form action="#" method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required placeholder="Masukkan nama Anda">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="email@contoh.com">
                    </div>
                    <div class="form-group">
                        <label for="message">Pesan</label>
                        <textarea id="message" name="message" rows="5" required placeholder="Tulis pesan Anda di sini..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="social-links">
            <a href="#" target="_blank">Instagram</a>
            <a href="#" target="_blank">Twitter</a>
            <a href="#" target="_blank">Facebook</a>
            <a href="#" target="_blank">LinkedIn</a>
        </div>
        <p>&copy; <?= date('Y') ?> CyberSoft Store. All rights reserved.</p>
    </footer>

    <button onclick="topFunction()" id="backToTop" title="Kembali ke Atas">⬆️</button>

    <!-- Chat Widget -->
    <button class="chat-button" onclick="toggleChat()">💬</button>

    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <span>Live Support</span>
            <span class="chat-close" onclick="toggleChat()">✖</span>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message received">Halo! Ada yang bisa kami bantu?</div>
        </div>
        <div class="chat-input-area">
            <input type="text" class="chat-input" id="chatInput" placeholder="Ketik pesan..." onkeypress="handleChatKey(event)">
            <button class="chat-send" onclick="sendMessage()">➤</button>
        </div>
    </div>

    <!-- Cookie Banner -->
    <div id="cookieBanner" class="cookie-banner">
        <div class="cookie-text">
            Kami menggunakan cookie untuk meningkatkan pengalaman Anda. Dengan melanjutkan, Anda menyetujui penggunaan cookie kami.
        </div>
        <button id="acceptCookie" class="cookie-btn">Saya Setuju</button>
    </div>

    <script>
        // Get the button
        let mybutton = document.getElementById("backToTop");

        // When the user scrolls down 200px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};

        function scrollFunction() {
            if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }

        // Scroll Animation Logic
        function isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return rect.top < window.innerHeight && rect.bottom > 0;
        }

        function animateOnScroll() {
            const elements = document.querySelectorAll('.scroll-animate, .slide-left, .slide-right');
            elements.forEach(el => {
                if (isInViewport(el) && !el.classList.contains('animate')) {
                    el.classList.add('animate');
                }
            });
        }

        // Modify onscroll to include animation
        window.onscroll = function() {
            scrollFunction();
            animateOnScroll();
        };

        // Also call on load
        window.addEventListener('load', animateOnScroll);

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        // Cookie Consent Logic
        const cookieBanner = document.getElementById('cookieBanner');
        const acceptCookieBtn = document.getElementById('acceptCookie');

        if (!localStorage.getItem('cookieConsent')) {
            cookieBanner.style.display = 'flex';
        }

        acceptCookieBtn.addEventListener('click', () => {
            localStorage.setItem('cookieConsent', 'true');
            cookieBanner.style.display = 'none';
        });

        // Chat Widget Logic
        function toggleChat() {
            const chatWindow = document.getElementById('chatWindow');
            if (chatWindow.style.display === 'flex') {
                chatWindow.style.display = 'none';
            } else {
                chatWindow.style.display = 'flex';
            }
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const messages = document.getElementById('chatMessages');
            const text = input.value.trim();

            if (text) {
                // Add user message
                const userMsg = document.createElement('div');
                userMsg.className = 'message sent';
                userMsg.textContent = text;
                messages.appendChild(userMsg);
                
                input.value = '';
                messages.scrollTop = messages.scrollHeight;

                // Simulate reply
                setTimeout(() => {
                    const replyMsg = document.createElement('div');
                    replyMsg.className = 'message received';
                    replyMsg.textContent = "Terima kasih atas pesan Anda. Tim kami akan segera merespons.";
                    messages.appendChild(replyMsg);
                    messages.scrollTop = messages.scrollHeight;
                }, 1000);
            }
        }

        function handleChatKey(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }

        function toggleMenu() {
            const hamburger = document.querySelector('.hamburger');
            const navLinks = document.querySelector('.nav-links');
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        }

    </script>

</body>
</html>
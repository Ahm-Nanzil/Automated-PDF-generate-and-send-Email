<?php

require_once __DIR__ .'/input/config.php';

$b = $conf['business'];

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" integrity="sha256-46qynGAkLSFpVbEBog43gvNhfrOj+BmwXdxFgVK/Kvc=" crossorigin="anonymous"
        />
        <link rel="stylesheet" href="./resources/css/style.css" />
        <title><?=$b['myCompany']?> | Welcome</title>
    </head>
    <body>
        <header>
            <nav id="main-nav">
                <div class="container">
                    <h2 class="home-logo"><?=$b['myCompany']?></span></h2>
                    <ul>
                        <li><a href="index.html" class="current">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                    <a href="#contact" class="btn quote-btn">Contact Us</a>
                </div>
            </nav>
            <div id="showpiece">
                <div class="container">
                    <h1 class="heading-lg">
                        Trademark Experts For Your Business
                    </h1>
                    <a href="#services" class="btn">Our Services</a>
                </div>
            </div>
        </header>

        <!-- Services Sections -->
        <section id="services">
            <div class="container">
                <h2 class="heading-med pad-y-3">
                    Our <span class="highlight">Services</span>
                </h2>
                <div class="service-container">
                    <div class="service-box">
                        <i class="icon-2 fas fa-money-bill fa-3x"></i>
                        <h2>Trademark Advice</h2>
                        <p>
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolor,
                            officia!
                        </p>
                    </div>
                    <div class="service-box">
                        <i class="icon-3 fas fa-brain fa-3x"></i>
                        <h2>In-depth Skills</h2>
                        <p>
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolor,
                            officia!
                        </p>
                    </div>
                    <div class="service-box">
                        <i class="icon-4 fas fa-clock fa-3x"></i>
                        <h2>24/7 Support</h2>
                        <p>
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolor,
                            officia!
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Business Stats Section -->
        <section id="stats">
            <div class="container">
                <h2 id="services-title" class="heading-med pad-y-3">Our Stats</h2>
                <div class="stats-container">
                    <div class="stat">
                        <i class="fas fa-smile fa-3x"></i>
                        <h2 class="pad-y-1">97%</h2>
                        <h2>Customer Satisifaction</h2>
                    </div>
                    <div class="stat">
                        <i class="fas fa-business-time fa-3x"></i>
                        <h2 class="pad-y-1">3500</h2>
                        <h2>Deals Made</h2>
                    </div>
                    <div class="stat">
                        <i class="fas fa-signal fa-3x"></i>
                        <h2 class="pad-y-1">99%</h2>
                        <h2>Business Success</h2>
                    </div>
                    <div class="stat">
                        <i class="fas fa-lightbulb fa-3x"></i>
                        <h2 class="pad-y-1">2156</h2>
                        <h2>Trademarks Protected</h2>
                    </div>
                </div>
            </div>
        </section>

        <!-- Consultants Section -->
        <section id="consultants" class="">
            <h2 id="section-title" class="heading-med">Why Choose Us?</h2>
            <div class="container">
                <div class="consultant-info">
                    <h2 class="heading-sml">
                        Because we're awesome!
                    </h2>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Minus porro
                        ex at quam similique delectus? Velit nesciunt, ea eveniet iste
                        veniam consectetur laudantium dolorum. Autem eum delectus dolorem
                        aperiam excepturi?
                    </p>
                </div>
                <img src="img/standup.jpg" alt="" />
            </div>
        </section>

        <!-- Phone Number Us Section -->
        <section id="phone">
            <div class="container">
                <h2 class="heading-med pad-y-1">Call us now</h2>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam,
                    sed.
                </p>
                <a href="#" class="btn"><?=$b['myPhone']?></a>
            </div>
        </section>

        <!-- Company info Section -->
        <section id="company-info" class="pad-y-3">
            <div class="container">
                <div class="footer-sections">
                    <div class="section">
                        <h2 class="home-logo"><?=$b['myCompany']?></h2>
                        <p><?=$b['myE-mail']?></p>
                        <p><?=$b['myPhone']?></p>
                        <p><?=$b['myStreet']?>, <?=$b['myCity']?></p>
                        <i class="fab fa-facebook-f"></i>
                        <i class="fab fa-twitter"></i>
                        <i class="fab fa-linkedin-in"></i>
                    </div>
                    <div class="section">
                        <h2>Services</h2>
                        <p>Trademark advice</p>
                        <p>In-depth Skills</p>
                        <p>24/7 Support</p>
                    </div>
                    <div class="section">
                        <h2>Links</h2>
                        <p>About</p>
                        <p>Case Studies</p>
                        <p>Contact</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <div class="container">
            <footer>
                Copyright &copy;2020 All rights reserved
            </footer>
        </div>
    </body>
</html>

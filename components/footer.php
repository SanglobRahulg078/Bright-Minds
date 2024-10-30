<footer id="footer" class="footer position-relative light-background">
    <div class="container">
        <div class="row gy-4 px-3">
            <div class="col-md-4 col-6 footer-about">
                <!-- <a href="index" class="logo d-flex align-items-center">
                    <span class="sitename">WriteQuest</span>
                </a> -->
                <h4>Contact Us</h4>
                <div class="footer-contact">
                    <!-- <p>First floor, VPHS School</p>
                    <p>Indra Chowk, Shahdol, MP, 484001 India</p> -->            
                    <!-- <p>India, USA/CANADA, UK</p> -->
                    <p><strong>Phone:</strong> <a href="tel: 2047278350"><span>+91 20 4727 8350</span></a></p>
                    <p><strong>Email:</strong> <a href="mailto:connect@sanglob.in">connect@sanglob.in</a></p>
                </div>
                <div class="social-links d-flex mt-3">
                    <a href=""><i class="bi bi-twitter-x"></i></a>
                    <a href=""><i class="bi bi-facebook"></i></a>
                    <a href=""><i class="bi bi-instagram"></i></a>
                    <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-md-3 col-6 footer-links">
                <h4>Useful Links</h4>
                <ul>
                    <!-- <li><a href="index#hero">Home</a></li>
                    <li><a href="index#about">About us</a></li>
                    <li><a href="index#services">Benefits</a></li> -->
                    <li><a href="term-condition">Terms of service</a></li>
                    <li><a href="privacy-policy">Privacy policy</a></li>
                </ul>
            </div>

            <div class="col-md-5 footer-newsletter">
                <h4>Our Newsletter</h4>
                <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
                <form action="forms/newsletter.php" method="post" class="php-email-form">
                    <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
                    <div class="loading">Loading</div>
                    <div class="error-message"></div>
                    <div class="sent-message">Your subscription request has been sent. Thank you!</div>
                </form>
            </div>
        </div>
    </div>

    <div class="container copyright text-center mt-2">
        <p>© <span>Copyright</span> <strong class="sitename">BrightMinds, </strong><span>All Rights Reserved.</span></p>
        <div class="credits">
            Designed by <a href="https://sanglob.in/" target="_blank">Sanglob.in</a>
        </div>
    </div>
</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php if($p_title != 'register' && $p_title != 'privacy-policy' && $p_title != 'term-condition' && $p_title != 'password-reset') { ?>
    <!-- Register Button -->
    <a href="register" id="register-btn" class="d-flex align-items-center justify-content-center btn-getstarted">Register Now</a>
<?php } ?>


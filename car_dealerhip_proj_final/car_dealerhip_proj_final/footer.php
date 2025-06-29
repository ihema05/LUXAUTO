<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>LUX<span style="color: var(--accent-color)">AUTO</span></h3>
                <p>Your premier destination for luxury vehicles and premium car parts.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="luxury-dealership.php">Home</a></li>
                    <li><a href="CarDearlship.php">Sales</a></li>
                    <li><a href="user_carparts.php">Parts</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>Phone: +20 1026085267</p>
                <p>Email: info@luxauto.com</p>
                <p>Address: 123 Luxury Street, Cairo, Egypt</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Luxury Auto Gallery. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
    footer {
        background: var(--primary-color);
        color: white;
        padding: 3rem 0 1rem;
        margin-top: 4rem;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .footer-section h3 {
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }

    .footer-section h4 {
        color: var(--accent-color);
        margin-bottom: 1rem;
        font-size: 1.2rem;
    }

    .footer-section p {
        margin: 0.5rem 0;
        color: rgba(255, 255, 255, 0.8);
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-section ul li {
        margin: 0.5rem 0;
    }

    .footer-section ul li a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-section ul li a:hover {
        color: var(--accent-color);
    }

    .footer-bottom {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-bottom p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .footer-section ul {
            display: inline-block;
            text-align: left;
        }
    }
</style> 
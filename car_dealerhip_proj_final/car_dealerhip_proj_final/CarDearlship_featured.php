<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Auto Gallery - Collection</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="CarCss.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <br> 
    <br>

    <section class="collection">
        <h2 class="section-title">Our Luxury Collection</h2>
        <div class="vehicles-grid">
            <div class="vehicle-card">
                <a href="https://www.mercedes-benz.com.eg/en/passengercars/models.html?group=amg&subgroup=see-all&filters=" target="_blank">
                    <img src="https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Luxury Car 1" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>Mercedes-Benz S-Class</h3>
                        <p class="price">$120,000</p>
                        <p>Experience ultimate luxury with the latest S-Class model</p>
                    </div>
                </a>
            </div>
            <div class="vehicle-card">
                <a href="https://www.bmw.com" target="_blank">
                    <img src="https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Luxury Car 2" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>BMW 7 Series</h3>
                        <p class="price">$95,000</p>
                        <p>Unparalleled comfort and performance</p>
                    </div>
                </a>
            </div>
            <div class="vehicle-card">
                <a href="https://www.audi.com" target="_blank">
                    <img src="https://images7.alphacoders.com/123/thumb-1920-1232824.jpg" alt="Luxury Car 3" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>Audi A8</h3>
                        <p class="price">$85,000</p>
                        <p>Innovative technology meets luxury</p>
                    </div>
                </a>
            </div>
            <div class="vehicle-card">
                <a href="https://www.jaguar.com" target="_blank">
                    <img src="http://images.classic.com/vehicles/04556d5c2db64aaf85e54283098c6222089b2668.jpg?auto=format&fit=crop&ar=16:9&w=6380" alt="Luxury Car 4" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>Jaguar XJ220</h3>
                        <p class="price">$110,000</p>
                        <p>Elegance and performance in one package</p>
                    </div>
                </a>
            </div>
            <div class="vehicle-card">
                <a href="https://www.lexus.com" target="_blank">
                    <img src="https://tse2.mm.bing.net/th/id/OIP.T49A5NHzrLIgjo6kvQlFhAHaEL?cb=iwp2&rs=1&pid=ImgDetMain" alt="Luxury Car 5" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>Lexus LS</h3>
                        <p class="price">$90,000</p>
                        <p>Luxury redefined with the Lexus LS</p>
                    </div>
                </a>
            </div>
            <div class="vehicle-card">
                <a href="https://www.porsche.com" target="_blank">
                    <img src="https://www.motortrend.com/uploads/sites/11/2012/09/2012-Porsche-Panamera-S-Hybrid-front-right-side-view1.jpg" alt="Luxury Car 6" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>Porsche Panamera</h3>
                        <p class="price">$130,000</p>
                        <p>Sporty and luxurious, the Panamera stands out</p>
                    </div>
                </a>
            </div>
            <div class="vehicle-card">
                <a href="https://www.rolls-roycemotorcars.com" target="_blank">
                    <img src="https://gaadiwaadi.com/wp-content/uploads/2021/10/2022-Rolls-Royce-Ghost-Black-Badge-1.jpg" alt="Luxury Car 7" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>Rolls-Royce Ghost</h3>
                        <p class="price">$300,000</p>
                        <p>Unparalleled luxury and craftsmanship</p>
                    </div>
                </a>
            </div>
            <div class="vehicle-card">
                <a href="https://www.bentleymotors.com" target="_blank">
                    <img src="https://www.motortrend.com/uploads/2022/12/2023-Bentley-Continental-GT-Mulliner-exterior-2.jpg" alt="Luxury Car 8" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>Bentley Continental GT</h3>
                        <p class="price">$220,000</p>
                        <p>British luxury at its finest</p>
                    </div>
                </a>
            </div>
            <div class="vehicle-card">
                <a href="https://www.maserati.com" target="_blank">
                    <img src="https://tse1.mm.bing.net/th?id=OIP.8FZ5xdI2Ql3ku-7eBvXo3gHaE8&pid=Api&P=0&h=220" alt="Luxury Car 9" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3>Maserati Quattroporte</h3>
                        <p class="price">$150,000</p>
                        <p>Italian luxury and performance</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>
</body>
</html>
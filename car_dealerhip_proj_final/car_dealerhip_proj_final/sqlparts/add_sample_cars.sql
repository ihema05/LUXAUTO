-- First, make sure we're using the right database
USE car_dealership;

-- Temporarily disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing cars to avoid duplicates
TRUNCATE TABLE cars;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Insert sample cars
INSERT INTO cars (make, model, year, price, description, image_url) VALUES
-- Mercedes-Benz
('Mercedes-Benz', 'S-Class', 2023, 120000.00, 'Experience ultimate luxury with the latest S-Class model', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('Mercedes-Benz', 'E-Class', 2023, 85000.00, 'Elegant and sophisticated, the E-Class sets new standards', 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('Mercedes-Benz', 'G-Class', 2023, 150000.00, 'Legendary off-road capability meets luxury', 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),

-- BMW
('BMW', '7 Series', 2023, 95000.00, 'Unparalleled comfort and performance', 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('BMW', 'X7', 2023, 110000.00, 'Luxury SUV with commanding presence', 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('BMW', 'M8', 2023, 130000.00, 'High-performance luxury coupe', 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),

-- Audi
('Audi', 'A8', 2023, 85000.00, 'Innovative technology meets luxury', 'https://images7.alphacoders.com/123/thumb-1920-1232824.jpg'),
('Audi', 'Q8', 2023, 95000.00, 'Premium SUV with cutting-edge design', 'https://images7.alphacoders.com/123/thumb-1920-1232824.jpg'),
('Audi', 'RS e-tron GT', 2023, 140000.00, 'Electric performance redefined', 'https://images7.alphacoders.com/123/thumb-1920-1232824.jpg'),

-- Porsche
('Porsche', 'Panamera', 2023, 130000.00, 'Sporty and luxurious, the Panamera stands out', 'https://www.motortrend.com/uploads/sites/11/2012/09/2012-Porsche-Panamera-S-Hybrid-front-right-side-view1.jpg'),
('Porsche', 'Cayenne', 2023, 115000.00, 'Luxury SUV with sports car DNA', 'https://www.motortrend.com/uploads/sites/11/2012/09/2012-Porsche-Panamera-S-Hybrid-front-right-side-view1.jpg'),
('Porsche', '911', 2023, 145000.00, 'Iconic sports car with unmatched heritage', 'https://www.motortrend.com/uploads/sites/11/2012/09/2012-Porsche-Panamera-S-Hybrid-front-right-side-view1.jpg'),

-- Lexus
('Lexus', 'LS', 2023, 90000.00, 'Luxury redefined with the Lexus LS', 'https://tse2.mm.bing.net/th/id/OIP.T49A5NHzrLIgjo6kvQlFhAHaEL?cb=iwp2&rs=1&pid=ImgDetMain'),
('Lexus', 'LX', 2023, 110000.00, 'Full-size luxury SUV with exceptional capability', 'https://tse2.mm.bing.net/th/id/OIP.T49A5NHzrLIgjo6kvQlFhAHaEL?cb=iwp2&rs=1&pid=ImgDetMain'),
('Lexus', 'LC', 2023, 95000.00, 'Stunning luxury coupe with hybrid technology', 'https://tse2.mm.bing.net/th/id/OIP.T49A5NHzrLIgjo6kvQlFhAHaEL?cb=iwp2&rs=1&pid=ImgDetMain'),

-- Bentley
('Bentley', 'Continental GT', 2023, 220000.00, 'Grand tourer with unmatched luxury', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('Bentley', 'Bentayga', 2023, 240000.00, 'Luxury SUV with extraordinary craftsmanship', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),

-- Rolls-Royce
('Rolls-Royce', 'Phantom', 2023, 450000.00, 'The pinnacle of automotive luxury', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('Rolls-Royce', 'Cullinan', 2023, 400000.00, 'Luxury SUV with unmatched presence', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),

-- Ferrari
('Ferrari', 'SF90 Stradale', 2023, 500000.00, 'Hybrid supercar with unprecedented performance', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('Ferrari', 'Purosangue', 2023, 400000.00, 'Ferrari\'s first SUV, redefining luxury', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),

-- Lamborghini
('Lamborghini', 'Aventador', 2023, 450000.00, 'V12-powered supercar with dramatic styling', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('Lamborghini', 'Urus', 2023, 250000.00, 'Super SUV with supercar performance', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),

-- Aston Martin
('Aston Martin', 'DB12', 2023, 250000.00, 'Grand tourer with British elegance', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('Aston Martin', 'DBX', 2023, 220000.00, 'Luxury SUV with Aston Martin DNA', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),

-- McLaren
('McLaren', 'Artura', 2023, 300000.00, 'Hybrid supercar with cutting-edge technology', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('McLaren', '720S', 2023, 350000.00, 'Supercar with extraordinary performance', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),

-- Bugatti
('Bugatti', 'Chiron', 2023, 3000000.00, 'The ultimate expression of automotive engineering', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'),
('Bugatti', 'Divo', 2023, 5800000.00, 'Track-focused hypercar with extreme aerodynamics', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');

-- Update car images with unique and appropriate URLs for each model
-- Mercedes-Benz
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Mercedes-Benz' AND model = 'S-Class';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Mercedes-Benz' AND model = 'E-Class';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Mercedes-Benz' AND model = 'G-Class';

-- BMW
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1553440569-bcc63803a83d' WHERE make = 'BMW' AND model = '7 Series';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1553440569-bcc63803a83d' WHERE make = 'BMW' AND model = 'X7';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1553440569-bcc63803a83d' WHERE make = 'BMW' AND model = 'M8';

-- Audi
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a' WHERE make = 'Audi' AND model = 'A8';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a' WHERE make = 'Audi' AND model = 'Q8';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a' WHERE make = 'Audi' AND model = 'RS e-tron GT';

-- Porsche
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Porsche' AND model = 'Panamera';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Porsche' AND model = 'Cayenne';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Porsche' AND model = '911';

-- Lexus
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Lexus' AND model = 'LS';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Lexus' AND model = 'LX';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Lexus' AND model = 'LC';

-- Bentley
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Bentley' AND model = 'Continental GT';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Bentley' AND model = 'Bentayga';

-- Rolls-Royce
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Rolls-Royce' AND model = 'Phantom';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Rolls-Royce' AND model = 'Cullinan';

-- Ferrari
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Ferrari' AND model = 'SF90 Stradale';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Ferrari' AND model = 'Purosangue';

-- Lamborghini
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Lamborghini' AND model = 'Aventador';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Lamborghini' AND model = 'Urus';

-- Aston Martin
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Aston Martin' AND model = 'DB12';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Aston Martin' AND model = 'DBX';

-- McLaren
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'McLaren' AND model = 'Artura';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'McLaren' AND model = '720S';

-- Bugatti
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Bugatti' AND model = 'Chiron';
UPDATE cars SET image_url = 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8' WHERE make = 'Bugatti' AND model = 'Divo'; 
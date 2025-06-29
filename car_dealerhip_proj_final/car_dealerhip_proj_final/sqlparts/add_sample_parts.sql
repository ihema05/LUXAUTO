-- First, make sure we're using the right database
USE car_dealership;

-- Temporarily disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing parts to avoid duplicates
TRUNCATE TABLE car_parts;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Insert sample car parts
INSERT INTO car_parts (name, type, brand, model, year, price, description, stock) VALUES
-- Engine Parts
('AMG Performance Exhaust System', 'engine', 'mercedes', 'AMG GT', 2023, 4500.00, 'High-performance exhaust system for AMG GT models', 10),
('M TwinPower Turbo Engine Kit', 'engine', 'bmw', 'M4', 2023, 8500.00, 'Complete turbo upgrade kit for M4 models', 5),
('RS Performance Air Intake', 'engine', 'audi', 'RS6', 2023, 1200.00, 'High-flow air intake system for RS6 models', 15),
('911 GT3 Engine Mounts', 'engine', 'porsche', '911', 2023, 800.00, 'Performance engine mounts for 911 GT3', 8),

-- Transmission Parts
('AMG Speedshift Transmission', 'transmission', 'mercedes', 'C63 AMG', 2023, 3500.00, 'Performance transmission upgrade for C63 AMG', 6),
('M DCT Transmission Kit', 'transmission', 'bmw', 'M3', 2023, 4200.00, 'Dual-clutch transmission upgrade kit', 4),
('RS Sport Differential', 'transmission', 'audi', 'RS7', 2023, 2800.00, 'Sport differential for RS7 models', 12),
('PDK Transmission Upgrade', 'transmission', 'porsche', 'Cayman', 2023, 3800.00, 'PDK transmission performance upgrade', 7),

-- Suspension Parts
('AMG Sport Suspension Kit', 'suspension', 'mercedes', 'E63 AMG', 2023, 2200.00, 'Complete sport suspension upgrade', 9),
('M Adaptive Suspension', 'suspension', 'bmw', 'M5', 2023, 2800.00, 'Adaptive suspension system for M5', 5),
('RS Sport Suspension', 'suspension', 'audi', 'RS5', 2023, 2500.00, 'Sport suspension kit for RS5', 8),
('PASM Suspension Upgrade', 'suspension', 'porsche', 'Panamera', 2023, 3200.00, 'Porsche Active Suspension Management upgrade', 6),

-- Brake Parts
('AMG Ceramic Brake Kit', 'brakes', 'mercedes', 'S63 AMG', 2023, 8500.00, 'Ceramic brake system upgrade', 4),
('M Carbon Ceramic Brakes', 'brakes', 'bmw', 'M8', 2023, 9200.00, 'Carbon ceramic brake system', 3),
('RS Ceramic Brake Kit', 'brakes', 'audi', 'RS e-tron GT', 2023, 8800.00, 'Ceramic brake upgrade for RS e-tron', 5),
('PCCB Brake System', 'brakes', 'porsche', '911', 2023, 9500.00, 'Porsche Ceramic Composite Brakes', 4),

-- Interior Parts
('AMG Performance Steering Wheel', 'interior', 'mercedes', 'AMG GT', 2023, 1200.00, 'Carbon fiber steering wheel with AMG logo', 15),
('M Performance Interior Trim', 'interior', 'bmw', 'M4', 2023, 1800.00, 'Carbon fiber interior trim kit', 10),
('RS Sport Seats', 'interior', 'audi', 'RS6', 2023, 2500.00, 'Sport bucket seats with RS logo', 8),
('GT3 Interior Package', 'interior', 'porsche', '911', 2023, 3200.00, 'Complete GT3 interior upgrade', 6),

-- Exterior Parts
('AMG Carbon Fiber Body Kit', 'exterior', 'mercedes', 'C63 AMG', 2023, 4500.00, 'Complete carbon fiber body kit', 7),
('M Performance Aero Kit', 'exterior', 'bmw', 'M3', 2023, 3800.00, 'Aerodynamic body kit', 9),
('RS Sport Body Kit', 'exterior', 'audi', 'RS7', 2023, 4200.00, 'Sport body kit with RS styling', 8),
('GT3 Aero Package', 'exterior', 'porsche', '911', 2023, 5500.00, 'Complete GT3 aerodynamic package', 5),

-- Additional Engine Parts
('AMG Performance ECU', 'engine', 'mercedes', 'E63 AMG', 2023, 1800.00, 'Performance ECU upgrade', 12),
('M Performance Chip', 'engine', 'bmw', 'M5', 2023, 1500.00, 'Engine control unit upgrade', 10),
('RS Performance Software', 'engine', 'audi', 'RS6', 2023, 1600.00, 'Engine management software upgrade', 15),
('Porsche Performance ECU', 'engine', 'porsche', 'Cayman', 2023, 1700.00, 'Engine control unit upgrade', 8),

-- Additional Transmission Parts
('AMG Performance Clutch', 'transmission', 'mercedes', 'AMG GT', 2023, 2200.00, 'Performance clutch kit', 6),
('M Performance Flywheel', 'transmission', 'bmw', 'M4', 2023, 1800.00, 'Lightweight performance flywheel', 8),
('RS Performance Gearbox', 'transmission', 'audi', 'RS7', 2023, 4500.00, 'Performance gearbox upgrade', 4),
('Porsche Performance Clutch', 'transmission', 'porsche', '911', 2023, 2800.00, 'Performance clutch system', 5); 
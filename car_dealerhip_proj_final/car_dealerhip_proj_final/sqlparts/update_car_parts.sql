-- First, make sure we're using the right database
USE car_dealership;

-- Add new columns to car_parts table
ALTER TABLE car_parts
ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER year,
ADD COLUMN description TEXT AFTER price,
ADD COLUMN stock INT NOT NULL DEFAULT 0 AFTER description;

-- Update prices for existing parts
UPDATE car_parts SET 
    price = 4500.00,
    description = 'High-performance exhaust system for AMG GT models',
    stock = 10
WHERE name = 'AMG Performance Exhaust System';

UPDATE car_parts SET 
    price = 8500.00,
    description = 'Complete turbo upgrade kit for M4 models',
    stock = 5
WHERE name = 'M TwinPower Turbo Engine Kit';

UPDATE car_parts SET 
    price = 1200.00,
    description = 'High-flow air intake system for RS6 models',
    stock = 15
WHERE name = 'RS Performance Air Intake';

UPDATE car_parts SET 
    price = 800.00,
    description = 'Performance engine mounts for 911 GT3',
    stock = 8
WHERE name = '911 GT3 Engine Mounts';

UPDATE car_parts SET 
    price = 3500.00,
    description = 'Performance transmission upgrade for C63 AMG',
    stock = 6
WHERE name = 'AMG Speedshift Transmission';

UPDATE car_parts SET 
    price = 4200.00,
    description = 'Dual-clutch transmission upgrade kit',
    stock = 4
WHERE name = 'M DCT Transmission Kit';

UPDATE car_parts SET 
    price = 2800.00,
    description = 'Sport differential for RS7 models',
    stock = 12
WHERE name = 'RS Sport Differential';

UPDATE car_parts SET 
    price = 3800.00,
    description = 'PDK transmission performance upgrade',
    stock = 7
WHERE name = 'PDK Transmission Upgrade';

UPDATE car_parts SET 
    price = 2200.00,
    description = 'Complete sport suspension upgrade',
    stock = 9
WHERE name = 'AMG Sport Suspension Kit';

UPDATE car_parts SET 
    price = 2800.00,
    description = 'Adaptive suspension system for M5',
    stock = 5
WHERE name = 'M Adaptive Suspension';

UPDATE car_parts SET 
    price = 2500.00,
    description = 'Sport suspension kit for RS5',
    stock = 8
WHERE name = 'RS Sport Suspension';

UPDATE car_parts SET 
    price = 3200.00,
    description = 'Porsche Active Suspension Management upgrade',
    stock = 6
WHERE name = 'PASM Suspension Upgrade';

UPDATE car_parts SET 
    price = 8500.00,
    description = 'Ceramic brake system upgrade',
    stock = 4
WHERE name = 'AMG Ceramic Brake Kit';

UPDATE car_parts SET 
    price = 9200.00,
    description = 'Carbon ceramic brake system',
    stock = 3
WHERE name = 'M Carbon Ceramic Brakes';

UPDATE car_parts SET 
    price = 8800.00,
    description = 'Ceramic brake upgrade for RS e-tron',
    stock = 5
WHERE name = 'RS Ceramic Brake Kit';

UPDATE car_parts SET 
    price = 9500.00,
    description = 'Porsche Ceramic Composite Brakes',
    stock = 4
WHERE name = 'PCCB Brake System';

UPDATE car_parts SET 
    price = 1200.00,
    description = 'Carbon fiber steering wheel with AMG logo',
    stock = 15
WHERE name = 'AMG Performance Steering Wheel';

UPDATE car_parts SET 
    price = 1800.00,
    description = 'Carbon fiber interior trim kit',
    stock = 10
WHERE name = 'M Performance Interior Trim';

UPDATE car_parts SET 
    price = 2500.00,
    description = 'Sport bucket seats with RS logo',
    stock = 8
WHERE name = 'RS Sport Seats';

UPDATE car_parts SET 
    price = 3200.00,
    description = 'Complete GT3 interior upgrade',
    stock = 6
WHERE name = 'GT3 Interior Package';

UPDATE car_parts SET 
    price = 4500.00,
    description = 'Complete carbon fiber body kit',
    stock = 7
WHERE name = 'AMG Carbon Fiber Body Kit';

UPDATE car_parts SET 
    price = 3800.00,
    description = 'Aerodynamic body kit',
    stock = 9
WHERE name = 'M Performance Aero Kit';

UPDATE car_parts SET 
    price = 4200.00,
    description = 'Sport body kit with RS styling',
    stock = 8
WHERE name = 'RS Sport Body Kit';

UPDATE car_parts SET 
    price = 5500.00,
    description = 'Complete GT3 aerodynamic package',
    stock = 5
WHERE name = 'GT3 Aero Package';

UPDATE car_parts SET 
    price = 1800.00,
    description = 'Performance ECU upgrade',
    stock = 12
WHERE name = 'AMG Performance ECU';

UPDATE car_parts SET 
    price = 1500.00,
    description = 'Engine control unit upgrade',
    stock = 10
WHERE name = 'M Performance Chip';

UPDATE car_parts SET 
    price = 1600.00,
    description = 'Engine management software upgrade',
    stock = 15
WHERE name = 'RS Performance Software';

UPDATE car_parts SET 
    price = 1700.00,
    description = 'Engine control unit upgrade',
    stock = 8
WHERE name = 'Porsche Performance ECU';

UPDATE car_parts SET 
    price = 2200.00,
    description = 'Performance clutch kit',
    stock = 6
WHERE name = 'AMG Performance Clutch';

UPDATE car_parts SET 
    price = 1800.00,
    description = 'Lightweight performance flywheel',
    stock = 8
WHERE name = 'M Performance Flywheel';

UPDATE car_parts SET 
    price = 4500.00,
    description = 'Performance gearbox upgrade',
    stock = 4
WHERE name = 'RS Performance Gearbox';

UPDATE car_parts SET 
    price = 2800.00,
    description = 'Performance clutch system',
    stock = 5
WHERE name = 'Porsche Performance Clutch'; 
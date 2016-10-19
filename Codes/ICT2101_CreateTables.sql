-- Create table script for users
Create Table Users
(
u_id INT NOT NULL PRIMARY KEY IDENTITY(1,1),
userName VARCHAR(255) NOT NULL,
password VARCHAR(255) NOT NULL,
r_id INT NOT NULL,
firstName VARCHAR(255),
lastName VARCHAR(255),
nric VARCHAR(20),
contactNumber VARCHAR(20)
)

-- Create table script for vehicles
-- Table is used to store information of the vehicle such as plate number, brand, model, year_of_purchase, type, driver
Create Table Vehicles 
(
v_id INT NOT NULL PRIMARY KEY IDENTITY(1,1),
plateNumber VARCHAR(50) NOT NULL,
brand VARCHAR(50) NOT NULL,
model VARCHAR(100) NOT NULL,
year_of_purchase VARCHAR(20),
u_id INT NOT NULL FOREIGN KEY REFERENCES Users(u_id)
)

-- Create table script for packages
-- Table is used to store information of the package such as order number, date of delivery, time of delivery, location, unit number, postal code
Create Table Packages
(
p_id INT NOT NULL PRIMARY KEY IDENTITY(1,1),
order_Number VARCHAR(255) NOT NULL,
delivery_Date DATETIME NOT NULL,
location VARCHAR(255) NOT NULL,
unitNumber VARCHAR(100),
postalCode VARCHAR(6)
)

-- Create table script for road maps
-- Table contain information on starting location, destination, vehicle that is sending the item, estimate time of delivery
Create Table RoadMaps
(
r_id INT NOT NULL PRIMARY KEY IDENTITY(1,1),
startLocation VARCHAR(255),
p_id INT NOT NULL FOREIGN KEY REFERENCES Packages(p_id),
eta INT,
v_id INT NOT NULL FOREIGN KEY REFERENCES Vehicles(v_id)
)

-- Create table script for Roles
Create Table Roles
(
r_id INT NOT NULL PRIMARY KEY IDENTITY(1,1),
name VARCHAR(255) NOT NULL
)

INSERT INTO Roles(name)
VALUES ('Dispatch Manager'), ('Driver')

SET search_path TO lbaw25145;

-- Insert sample data

-- Users
INSERT INTO "user" (username, email, name, surname, password) VALUES
('admin', 'admin@example.com', 'Admin', 'User', 'password'),
('owner1', 'owner1@example.com', 'Restaurant', 'Owner1', 'password'),
('owner2', 'owner2@example.com', 'Restaurant', 'Owner2', 'password'),
('customer1', 'customer1@example.com', 'John', 'Doe', 'password'),
('customer2', 'customer2@example.com', 'Jane', 'Smith', 'password');

-- Roles
INSERT INTO "administrator" (id) VALUES (1);
INSERT INTO "owner" (id) VALUES (2), (3);
INSERT INTO "customer" (id) VALUES (4), (5);

-- Restaurants
INSERT INTO "restaurant" (owner_id, name, description, email, phone_number, address, opening_hours, capacity) VALUES
(2, 'The Gourmet Place', 'A fine dining experience.', 'gourmet@example.com', '123456789', '123 Gourmet Street', '18:00-23:00', 50),
(3, 'Pizza Heaven', 'The best pizza in town.', 'pizza@example.com', '987654321', '456 Pizza Avenue', '12:00-22:00', 30);

-- Reviews
INSERT INTO "review" (user_id, restaurant_id, content, rating) VALUES
(4, 1, 'Excellent food and service!', 5),
(5, 1, 'A bit pricey, but worth it.', 4),
(4, 2, 'Amazing pizza, will come back!', 5);

-- Reservations
INSERT INTO "reservation" (user_id, restaurant_id, number_of_people, date_of_visit, time_of_visit, is_confirmed)
VALUES
    (4, 1, 2, '2025-11-15', '20:00:00', true),
    (5, 2, 4, '2025-11-20', '19:30:00', true);

-- Favourites
INSERT INTO "favourite" (user_id, restaurant_id) VALUES
(4, 1),
(5, 2);

SET search_path TO lbaw25145;

-- Limpar tudo antes de popular
TRUNCATE TABLE
    "user", "administrator", "customer", "owner", "restaurant",
    "favourite", "review", "reply", "restaurant_photo", "reservation",
    "waitlist", "offer", "notification", "review_notification",
    "reservation_notification", "offer_notification"
RESTART IDENTITY CASCADE;

-- Desativar triggers para permitir inserção massiva sem validações chatas
ALTER TABLE reservation DISABLE TRIGGER validate_opening_hours;
ALTER TABLE reservation DISABLE TRIGGER validate_reservation_changes;

-- 1. USERS (Password: '12345678' hashada)
INSERT INTO "user" (username, email, name, surname, password, profile_description)
VALUES
    ('root_admin', 'admin@eatz.com', 'Alice', 'Root', '$2y$12$QapHwKREapAyRHXJAIOu..hokWEyvF9KF2xSnjeMymCHn5Z85gPU2', 'Lead platform administrator.'),
    ('sec_admin', 'security@eatz.com', 'Bob', 'Secure', '$2y$12$QapHwKREapAyRHXJAIOu..hokWEyvF9KF2xSnjeMymCHn5Z85gPU2', 'Security and compliance officer.'),
    ('chef_charles', 'charles@gourmet.com', 'Charles', 'Pascale', '$2y$12$RYFJbQJ3zWwIzNC7QDGLOObebU1SZAm6lnoyd35cJtOUOgOI649CO', 'Owner and head chef of The Gourmet Place.'),
    ('diana_mgr', 'diana@italian.com', 'Diana', 'Rossi', '$2y$12$RYFJbQJ3zWwIzNC7QDGLOObebU1SZAm6lnoyd35cJtOUOgOI649CO', 'Manager of Pizza Heaven and Pasta Palace.'),
    ('eve_brunch', 'eve@cozy.com', 'Eve', 'Adams', '$2y$12$RYFJbQJ3zWwIzNC7QDGLOObebU1SZAm6lnoyd35cJtOUOgOI649CO', 'Proprietor of The Cozy Corner.'),
    ('frank_sushi', 'frank@sushi.com', 'Frank', 'Tanaka', '$2y$12$RYFJbQJ3zWwIzNC7QDGLOObebU1SZAm6lnoyd35cJtOUOgOI649CO', 'Sushi master and owner of Sushi Central.'),
    ('grace_foodie', 'grace@email.com', 'Grace', 'Hopper', '$2y$12$eCb5XLQFN6ILh3y8E3tRNun4Q4PZ45AMy5jrdYxTLeRzH35jMq.AS', 'Food enthusiast and blogger. Love trying new things!'),
    ('heidi_eats', 'heidi@email.com', 'Heidi', 'Lamarr', '$2y$12$eCb5XLQFN6ILh3y8E3tRNun4Q4PZ45AMy5jrdYxTLeRzH35jMq.AS', 'I enjoy fine dining and quiet ambiances.'),
    ('ivan_reviews', 'ivan@email.com', 'Ivan', 'Sutherland', '$2y$12$eCb5XLQFN6ILh3y8E3tRNun4Q4PZ45AMy5jrdYxTLeRzH35jMq.AS', 'Just a regular guy who loves a good meal.'),
    ('judy_dines', 'judy@email.com', 'Judy', 'Martins', '$2y$12$eCb5XLQFN6ILh3y8E3tRNun4Q4PZ45AMy5jrdYxTLeRzH35jMq.AS', 'Casual diner, pizza lover.'),
    ('kevin_hacks', 'kevin@email.com', 'Kevin', 'Mitnick', '$2y$12$eCb5XLQFN6ILh3y8E3tRNun4Q4PZ45AMy5jrdYxTLeRzH35jMq.AS', 'Looking for the best deals and happy hours.'),
    ('laura_p', 'laura@email.com', 'Laura', 'Palmer', '$2y$12$eCb5XLQFN6ILh3y8E3tRNun4Q4PZ45AMy5jrdYxTLeRzH35jMq.AS', 'I celebrate all my special occasions by dining out.');

INSERT INTO "user" (username, email, name, surname, password, is_blocked, profile_description)
VALUES
    ('blocked_user', 'blocked@email.com', 'Blocked', 'User', '$2y$12$eCb5XLQFN6ILh3y8E3tRNun4Q4PZ45AMy5jrdYxTLeRzH35jMq.AS', true, 'This account has been blocked by administrators.');

-- 2. ROLES
INSERT INTO "administrator" (id) VALUES (1), (2);
INSERT INTO "owner" (id) VALUES (3), (4), (5), (6);
INSERT INTO "customer" (id) VALUES (7), (8), (9), (10), (11), (12), (13);

-- 3. RESTAURANTS (Correção JSONB)
INSERT INTO "restaurant" (owner_id, name, description, email, phone_number, address, opening_hours, capacity)
VALUES
    (3, 'The Gourmet Place', 'A fine dining experience.', 'contact@gourmetplace.com', '111222333', '123 Gourmet Street, Porto', 
    '{"mon":["12:00-15:00"],"tue":["12:00-15:00"],"wed":["12:00-15:00"],"thu":["12:00-15:00"],"fri":["13:00-19:00"],"sat":["12:00-23:59"],"sun":[]}', 40),
    
    (4, 'Pizza Heaven', 'Authentic Italian pizza.', 'ciao@pizzaheaven.com', '444555666', '456 Pizza Avenue, Porto', 
    '{"mon":["12:00-15:00"],"tue":["12:00-15:00"],"wed":["12:00-15:00"],"thu":["12:00-15:00"],"fri":["13:00-19:00"],"sat":["12:00-23:59"],"sun":[]}', 60),
    
    (4, 'Pasta Palace', 'Homemade pasta.', 'info@pastapalace.com', '777888999', '789 Pasta Lane, Matosinhos', 
    '{"mon":["12:00-15:00"],"tue":["12:00-15:00"],"wed":["12:00-15:00"],"thu":["12:00-18:00"],"fri":["13:00-19:00"],"sat":["12:00-23:59"],"sun":[]}', 50),
    
    (5, 'The Cozy Corner', 'A charming cafe.', 'hello@cozycorner.com', '123456789', '1 Cafe Street, Gaia', 
    '{"mon":["11:00-17:00"],"tue":["12:00-15:00"],"wed":["12:00-15:00"],"thu":["12:00-15:00"],"fri":["13:00-19:00"],"sat":["12:00-23:59"],"sun":[]}', 20),
    
    (6, 'Sushi Central', 'Fresh sushi.', 'sushi@central.com', '987654321', '2 Sushi Boulevard, Porto', 
    '{"mon":["10:00-16:00"],"tue":["12:00-15:00"],"wed":["12:00-15:00"],"thu":["10:00-15:00"],"fri":["13:00-19:00"],"sat":["12:00-23:59"],"sun":[]}', 70);

-- 4. PHOTOS
INSERT INTO restaurant_photo (restaurant_id, link, title, display_order) VALUES
(1, 'gp_dish.jpg', 'Our signature codfish confit', 1),
(1, 'gp_interior.jpg', 'The main dining hall', 2),
(1, 'gp_dessert.jpg', 'Chocolate fondant', 3),
(2, 'ph_pizza.jpg', 'Wood-fired Margherita', 1),
(3, 'pp_pasta.jpg', 'Freshly made carbonara', 1),
(4, 'cc_brunch.jpg', 'Avocado toast', 1),
(5, 'sc_sushi.jpg', 'Deluxe sushi platter', 1);

-- 5. RESERVATIONS
INSERT INTO reservation (user_id, restaurant_id, number_of_people, date_of_visit, time_of_visit, is_confirmed, is_completed, title, description, created_at) VALUES
(7, 1, 2, '2025-03-12', '19:00:00', true, true, 'Anniversary Dinner', 'Celebrating our 5th anniversary', '2025-03-10'),
(8, 2, 4, '2025-04-14', '18:30:00', true, true, 'Family Dinner', 'Birthday celebration for my daughter', '2025-04-10'),
(9, 1, 2, '2025-05-13', '19:30:00', true, true, 'Date Night', NULL, '2025-05-10'),
(10, 3, 2, '2025-06-11', '13:00:00', true, true, 'Lunch Meeting', 'Business lunch with client', '2025-06-09'),
(7, 3, 2, '2025-03-14', '14:00:00', true, true, 'Dinner Reservation', NULL, '2025-03-10'),
(8, 1, 2, '2025-03-17', '19:00:00', true, true, 'Special Dinner', 'Special occasion dinner', '2025-03-15'),
(9, 2, 3, '2025-04-16', '18:30:00', true, true, 'Friends Gathering', NULL, '2025-04-10'),
(10, 2, 2, '2025-03-18', '12:30:00', true, true, 'Casual Dinner', NULL, '2025-03-15');

INSERT INTO reservation (user_id, restaurant_id, number_of_people, date_of_visit, time_of_visit, is_confirmed, is_completed, title, description, created_at) VALUES
(7, 3, 2, '2025-03-12', '14:00:00', true, false, 'Weekend Dinner', 'Trying their new menu', '2025-03-05 10:17:23'),
(8, 5, 6, '2025-04-15', '12:30:00', true, false, 'Family Celebration', 'Grandma''s birthday party', '2025-04-10'),
(11, 4, 2, '2025-05-14', '13:00:00', true, false, 'Brunch Date', NULL, '2025-05-10'),
(9, 1, 4, '2025-06-13', '18:00:00', true, false, 'Double Date', 'Dinner with friends', '2025-06-01'),
(12, 5, 3, '2025-07-16', '13:00:00', true, false, 'Sushi Night', NULL, '2025-07-10');

INSERT INTO reservation (user_id, restaurant_id, number_of_people, date_of_visit, time_of_visit, is_confirmed, is_completed, title, created_at) VALUES
(12, 2, 5, '2025-03-17', '12:20:00', false, false, 'Group Dinner', '2025-03-08'),
(7, 4, 8, '2025-12-30', '12:30:00', false, false, 'Sunday Brunch', '2025-05-12'),
(11, 4, 2, '2025-12-30', '13:00:00', false, false, 'Dinner Reservation', '2025-05-12');

INSERT INTO reservation (user_id, restaurant_id, number_of_people, date_of_visit, time_of_visit, is_confirmed, is_completed, created_at) VALUES
(7, 4, 2, '2025-12-24', '13:30:00', true, false, '2025-11-12 16:12:14');

INSERT INTO reservation (user_id, restaurant_id, number_of_people, date_of_visit, time_of_visit, is_confirmed, is_completed, title, description, created_at) VALUES
(7, 4, 7, '2025-12-18', '13:00:00', false, true, 'Jantar with Jabba', 'Yummm', '2025-11-10 19:30:00'),
(7, 4, 2, '2025-12-13', '12:30:00', true, false, 'Lunch with Vernon Roche', 'For Temeria', '2025-11-05 23:32:00'),
(7, 4, 4, '2025-12-25', '13:00:00', false, false, 'Lone fine dining experience', NULL, '2025-12-10 14:11:00');

-- 6. REVIEWS & REPLIES
-- 6. REVIEWS & REPLIES (Commented out for A8 Prototype)
/*
INSERT INTO "review" (user_id, restaurant_id, content, rating, created_at) VALUES 
(7, 1, 'Absolutely fantastic!', 5, '2024-10-16');
INSERT INTO "reply" (user_id, review_id, content, created_at) VALUES 
(3, 1, 'Thank you so much, Grace!', '2024-10-16');

INSERT INTO "review" (user_id, restaurant_id, content, rating, created_at) VALUES 
(8, 2, 'Great pizza.', 3, '2024-09-21');
INSERT INTO "reply" (user_id, review_id, content, created_at) VALUES 
(4, 2, 'Thank you for your feedback.', '2024-09-21');
*/

-- 7. OFFERS
INSERT INTO offer (restaurant_id, title, content, start_date, end_date) VALUES
(2, '2-for-1 Pizzas', 'Buy one get one free', CURRENT_DATE - INTERVAL '1 day', CURRENT_DATE + INTERVAL '30 days'),
(5, 'Sushi Happy Hour', '30% off', CURRENT_DATE, CURRENT_DATE + INTERVAL '60 days');

-- 8. FAVOURITES
INSERT INTO favourite (user_id, restaurant_id) VALUES
(7, 1), (7, 3), (8, 1), (9, 1), (10, 3);

-- 9. WAITLIST
-- (Nenhum exemplo ativo para evitar erros de referência se a reserva não existir)

-- 10. NOTIFICATIONS
INSERT INTO notification (user_id, title, content, date, viewed) VALUES
(3, 'New Review Posted', 'Grace has posted a review', '2024-10-16', true),
(7, 'Reply to Your Review', 'Owner replied', '2024-10-16', true);

-- Reativar triggers
ALTER TABLE reservation ENABLE TRIGGER validate_opening_hours;
ALTER TABLE reservation ENABLE TRIGGER validate_reservation_changes;
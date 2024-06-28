-- Active: 1718734454466@@127.0.0.1@3306

DROP Table users

CREATE Table users(
    id INT PRIMARY KEY AUTO_INCREMENT,
    fristName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    phone VARCHAR(100) NOT NULL UNIQUE,
    address VARCHAR(100) NOT NULL,
    `bloodGroup` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    DOB DATE NOT NULL,
    citizenshipNo VARCHAR(100),
    licenseNo VARCHAR(100),
    isRider ENUM('yes', 'no') DEFAULT 'no',
    rating INT DEFAULT 0,
    token VARCHAR(100) DEFAULT NULL,
    tokenExpiry DATETIME DEFAULT NULL
)

-- insert dummy user into the table users
INSERT INTO
    users (
        fristName,
        lastName,
        email,
        password,
        phone,
        address,
        DOB,
        citizenshipNo,
        licenseNo,
        isRider
    )
VALUES (
        'Jane',
        'Doe',
        'arunstha5471@gmail.com',
        'user',
        '12347567890',
        '123 Main St, City, Country',
        '1991-01-25',
        '12345675890',
        '12345675890',
        'no');

-- insert dummy user into the table users
INSERT INTO
    users (
        fristName,
        lastName,
        email,
        password,
        phone,
        address,
        DOB,
        nagritaNo,
        licenseNo,
        isRider
    )
VALUES (
        'Jane',
        'Doe',
        'user@mail.com',
        'user',
        '1234fd567890',
        '123 Main St, City, Country',
        '1991-01-01',
        '123456fd7890',
        '1234fd567890',
        'no');

-- create a table named poolrequest with attributes id, userid, source, destination, vehicletype, seats, status, createdate, updatedate ---- source and destination are lattitude and longitude
CREATE TABLE poolrequests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    sourceAddress VARCHAR(255) NOT NULL,
    sourceLatitude DOUBLE NOT NULL,
    sourceLongitude DOUBLE NOT NULL,
    destinationAddress VARCHAR(255) NOT NULL,
    destinationLatitude DOUBLE NOT NULL,
    destinationLongitude DOUBLE NOT NULL,
    vehicleType ENUM('car', 'bike') NOT NULL,
    AppliedSeats INT NOT NULL,
    time DATETIME NOT NULL,
    date DATE NOT NULL,
    status ENUM('booked', 'available') DEFAULT 'available' NOT NULL,
    createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users (id)
);

--create a dummpy poolrequests using the info src: 27.664610794805267, 85.38568843834916, src_address: KSL and dest coordintes: 27.698051573844335, 85.32385058067794 and destination address as Singha Durbar, Leave no field empty, fill all of it
INSERT INTO
    poolrequests (
        userId,
        sourceAddress,
        sourceLatitude,
        sourceLongitude,
        destinationAddress,
        destinationLatitude,
        destinationLongitude,
        vehicleType,
        AppliedSeats,
        time,
        date
    )
VALUES (
        1, -- Assuming there's a user with id 1 in the users table
        'Kathmandu School of Law',
        27.664610794805267,
        85.38568843834916,
        'Tinkune',
        27.68228235535513, 
        85.34930274767896,
        'car',
        4,
        '2022-10-10 10:00:00',
        '2022-10-10'
    );


-- now create another one with src: 27.664610794805267, 85.38568843834916, src_address: KSL and destination as 27.694526274520637, 85.32031739602279, maitighar
INSERT INTO
    poolrequests (
        userId,
        sourceAddress,
        sourceLatitude,
        sourceLongitude,
        destinationAddress,
        destinationLatitude,
        destinationLongitude,
        vehicleType,
        AppliedSeats,
        time,
        date
    )
VALUES (
        1, -- Assuming there's a user with id 1 in the users table
        'Kathmandu School of Law',
        27.664610794805267,
        85.38568843834916,
        'Maitighar',
        27.694526274520637,
        85.32031739602279,
        'car',
        4,
        '2022-10-10 10:00:00',
        '2022-10-10'
    );

INSERT INTO
    users (
        firstname,
        lastname,
        email,
        pass,
        contact,
        address
    )
VALUES (
        'John',
        'Doe',
        'john.doe@example.com',
        'password_hash_here',
        '1234567890',
        '123 Main St, City, Country'
    );

INSERT INTO
    poolrequests (
        userid,
        source_latitude,
        source_longitude,
        destination_latitude,
        destination_longitude,
        vehicletype,
        seats
    )
VALUES (
        1, -- Assuming there's a user with id 1 in the users table
        27.663443949529345,
        85.38828250741429,
        27.674326132958143,
        85.36413311438034,
        'car',
        4
    );

-- create dummy insert sql for 27.673641928875046, 85.38780257724241 as src and 27.676651820504812, 85.34999179818696 as destination
INSERT INTO
    poolrequests (
        userid,
        source_latitude,
        source_longitude,
        destination_latitude,
        destination_longitude,
        vehicletype,
        seats,
        status
    )
VALUES (
        1, -- Assuming there's a user with id 1 in the users table
        27.673641928875046,
        85.38780257724241,
        27.676651820504812,
        85.34999179818696,
        'car',
        4,
        'verified'
    );

--  27.67014820061459, 85.4083162909988 27.659558834396172, 85.36346530268106
INSERT INTO
    poolrequests (
        userid,
        source_latitude,
        source_longitude,
        destination_latitude,
        destination_longitude,
        vehicletype,
        seats,
        status
    )
VALUES (
        1, -- Assuming there's a user with id 1 in the users table
        27.67014820061459,
        85.4083162909988,
        27.659558834396172,
        85.36346530268106,
        'car',
        4,
        'verified'
    );

--  27.64968616288505, 85.35888845129072 to 27.678269310918136, 85.34893209186998
INSERT INTO
    poolrequests (
        userid,
        source_address,
        destination_address,
        source_latitude,
        source_longitude,
        destination_latitude,
        destination_longitude,
        vehicletype,
        seats
    )
VALUES (
        1, -- Assuming there's a user with id 1 in the users table
        'Kathmandu School of Law',
        'Tinkune',
        27.661841797747275,
        85.38793789513242,
        27.683984069134052,
        85.34903925353315,
        'car',
        4
    );

-- src: 27.661841797747275, 85.38793789513242 : Kathmandu School of Law, dest = 27.683984069134052, 85.34903925353315: Tinkune, create a dummy poolrequest



CREATE TABLE poolalerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    sourceAddress VARCHAR(255) NOT NULL,
    sourceLatitude DOUBLE NOT NULL,
    sourceLongitude DOUBLE NOT NULL,
    destinationAddress VARCHAR(255) NOT NULL,
    destinationLatitude DOUBLE NOT NULL,
    destinationLongitude DOUBLE NOT NULL,
    vehicleType ENUM('car', 'bike') NOT NULL,
    vacantSeats INT NOT NULL CHECK (vacantSeats <= advertisedSeats),
    advertisedSeats INT NOT NULL,
    time TIME NOT NULL,
    date DATE NOT NULL,
    status ENUM('booked', 'available') DEFAULT 'available' NOT NULL,
    createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users (id)
);



-- create a table named poolmapping with attributes id, poolrequestid, poolalertid, status, is_new, createdate, updatedate where status is enum(read, unread) and is_new is boolean(yes, no)
CREATE TABLE poolmappings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    poolRequestId INT NOT NULL,
    poolAlertId INT NOT NULL,
    bookedSeats INT NOT NULL,
    status ENUM('read', 'unread') DEFAULT 'unread' NOT NULL,
    isNew ENUM('yes', 'no') DEFAULT 'yes' NOT NULL,
    createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (poolRequestId) REFERENCES poolrequests (id),
    FOREIGN KEY (poolAlertId) REFERENCES poolalerts (id)
);

-- insert dummy data into users
INSERT INTO
    users (
        firstname,
        lastname,
        email,
        pass,
        contact,
        address
    )
VALUES (
        'Jane',
        'Doe',
        'yo@gmail.com',
        'password_hash_here',
        '1234567890',
        '123 Main St, City, Country'
    );


--insert dummy data into poolalerts
INSERT INTO
    poolalerts (
        userid,
        sourceaddress,
        source_latitude,
        source_longitude,
        destinationaddress,
        destination_latitude,
        destination_longitude,
        vehicletype,
        vacantseats
    )
VALUES (
        2, -- Assuming there's a user with id 1 in the users table
        'Kathmandu, Nepal',
        27.675459911803472,
        85.39739390223428,
        'Bhaktapur, Nepal',
        27.673597625832617,
        85.38687964327265,
        'car',
        4
    );

-- insert dummy data into poolmapping
INSERT INTO
    poolmappings (
        poolrequestid,
        poolalertid
    )
VALUES (
        1, -- Assuming there's a poolrequest with id 1 in the poolrequests table
        1
    );




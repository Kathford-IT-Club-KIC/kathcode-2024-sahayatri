<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            height: 100%;
            /* Ensure the body takes full height */
            overflow: auto;
            /* Allow body to scroll if content overflows */
        }

        .preview-container {
            max-height: 100px;
            overflow: hidden;
            position: relative;
            /* Ensure relative positioning for proper stacking */
        }

        .preview-container img {
            display: block;
            max-width: 100%;
            height: auto;
            margin-top: 5px;
            /* Add margin to separate image from form elements */
        }

        .form-section {
            margin-bottom: 1rem;
            /* Add margin bottom to separate form sections */
        }
    </style>
</head>

<body class="flex items-center justify-center h-screen" style="background-color: #1f2937;">
    <div class="bg-gray-100 p-8 rounded-lg shadow-lg max-w-6xl w-full h-full overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6 text-center">Sign Up Driver</h2>

        <form id="signupForm" method="post" action="Includes/server.php" enctype="multipart/form-data">
            <div class="form-section flex space-x-4 mb-4">
                <div class="w-1/3">
                    <label class="block text-gray-700" for="first_name">First Name</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="text" id="first_name" name="first_name" required>
                </div>
                <!-- <div class="w-1/3">
                    <label class="block text-gray-700" for="middle_name">Middle Name</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="text" id="middle_name" name="middle_name" required>
                </div> -->
                <div class="w-1/3">
                    <label class="block text-gray-700" for="last_name">Last Name</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="text" id="last_name" name="last_name" required>
                </div>
            </div>
            <div class="form-section flex space-x-4 mb-4">
                <div class="w-1/3">
                    <label class="block text-gray-700" for="dob">Date of Birth</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="date" id="dob" name="dob" required>
                </div>

                <div class="w-1/3">
                    <label class="block text-gray-700" for="email">Email</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="email" id="email" name="email" required>
                </div>
                <div class="w-1/3">
                    <label class="block text-gray-700" for="address">Address</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="text" id="address" name="address" required>
                </div>
            </div>
            <div class="form-section flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700" for="are_you_driver">Are You a Driver?</label>
                    <select
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        id="are_you_driver" name="are_you_driver" onchange="toggleDriverFields(this.value)" required>
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700" for="blood_group">Blood Group</label>
                    <select
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        id="blood_group" name="blood_group" required>
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            </div>

            <div class="form-section flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700" for="nagarita_number">Citizenship Number</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="text" id="nagarita_number" name="nagarita_number" required>
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700" for="citizenship_photo">Citizenship Photo (PNG only)</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="file" id="citizenship_photo" name="citizenship_photo" accept="image/png"
                        onchange="previewImage(event, 'citizenship_preview')" required>
                    <div class="preview-container">
                        <img id="citizenship_preview" class="mt-2 rounded-lg"
                            style="display: none; max-height: 100px;" />
                    </div>
                </div>
            </div>
            <div id="driverFields" style="display: none;">
                <div class="form-section flex space-x-4 mb-4">
                    <div class="w-1/2">
                        <label class="block text-gray-700" for="number_plate">Number Plate</label>
                        <input
                            class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                            type="text" id="number_plate" name="number_plate">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700" for="driver_license_photo">Driver License Photo (PNG
                            only)</label>
                        <input
                            class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                            type="file" id="driver_license_photo" name="driver_license_photo" accept="image/png"
                            onchange="previewImage(event, 'driver_license_preview')">
                        <div class="preview-container">
                            <img id="driver_license_preview" class="mt-2 rounded-lg"
                                style="display: none; max-height: 100px;" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="form-section mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700" for="username">Username</label>
                    <input
                        class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        type="text" id="username" name="username" required>
                </div>
            </div> -->
            <div class="form-section mb-4">
                <label class="block text-gray-700" for="password">Password</label>
                <input
                    class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                    type="password" id="password" name="password_1" required>
            </div>
            <div class="form-section mb-4">
                <label class="block text-gray-700" for="confirm-password">Confirm Password</label>
                <input
                    class="w-full px-4 py-4 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                    type="password" id="confirm-password" name="password_2" required>
            </div>
            <div class="form-section mb-4">
                <input type="checkbox" id="agree-checkbox" required>
                <label for="agree-checkbox" class="inline-flex items-center cursor-pointer">
                    <span class="ml-2">I agree to the <a href="terms_and_condition.html" target="_blank"
                            class="text-blue-600 hover:underline">Terms and Conditions</a> and have read the <a
                            href="policy.html" target="_blank" class="text-blue-600 hover:underline">Privacy
                            Policy</a>.</span>
                </label>
            </div>
            <div class="flex flex-col items-center">
                <button type="submit" name="reg_user"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600">Sign
                    Up</button>
                <a href="Login.php" class="mt-4 text-sm text-blue-600 hover:underline">Already have an account?</a>
            </div>
        </form>
    </div>
    <script>
        // Function to toggle visibility of driver-related fields
        function toggleDriverFields(value) {
            const driverFields = document.getElementById('driverFields');
            if (value === 'yes') {
                driverFields.style.display = 'block';
            } else {
                driverFields.style.display = 'none';
                // Clear number plate and driver license photo if user selects "No"
                document.getElementById('number_plate').value = '';
                document.getElementById('driver_license_preview').src = '';
                document.getElementById('driver_license_preview').style.display = 'none';
            }
        }

        // Function to preview image
        function previewImage(event, previewId) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById(previewId);
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>
<?php
   session_start();
   include '../config.php';

   // Check if the user is logged in
   if (!isset($_SESSION['admin_logged_in'])) {
       header("Location: admin_login.php");
       exit();
   }
   
//    $user_id = $_SESSION['user_id'] ?? '';

   // Encryption/Decryption functions (same as in registration)
   function decrypt_password($encrypted_password, $encryption_key) {
      list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_password), 2);
      return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
   }

   // Secure encryption key (this should be the same as used during registration)
   $encryption_key = 'mysecretkey1234567890'; // Same key used for encryption
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Responsive Admin Portal</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            /* Basic Reset */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            /* Sidebar */
            .sidebar {
                width: 200px;
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                background-color: #3A6187;
                padding-top: 20px;
                transition: transform 0.3s ease;
                z-index: 1000;
            }

            .sidebar-nav {
                list-style-type: none;
                padding: 0;
            }

            .sidebar-nav li {
                margin: 20px 0;
            }

            .sidebar-nav li a {
                text-decoration: none;
                color: white;
                padding: 10px 20px;
                display: block;
                transition: background 0.3s;
            }

            .sidebar-nav li a:hover {
                background-color: #2e4e6f;
            }

            /* Backdrop */
            .backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: none;
                z-index: 999;
            }

            /* Main Content */
            .main-content {
                margin-left: 200px;
                padding: 10px;
                transition: margin-left 0.3s ease;
            }

            .menu-btn {
                display: none;
                background-color: #3A6187;
                color: white;
                padding: 2px 7px;
                cursor: pointer;
                position: absolute;
                top: 8px;
                left: 9px;
                z-index: 1001;
            }
            
            .parent_portal {
                margin-top: 15px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                background-color: #fff;
                padding: 15px 4px;
                border-radius: 10px;
            }
            
            .nowrap {white-space: nowrap;}
            .left {text-align: left;}
            .right {text-align: right;}
            .center {text-align: center;}
            
            #applicantsTable th{
                vertical-align:top;
                text-align:center;
                background-color: #9DA4B1;
                color: #000;
            }

            .dashboard table th{
                color: var(--white);
                font-weight: bold;
            }
            tr:has(th):hover {
                background-color: transparent;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .parent_portal img {
                height: 50px;
                width: 50px;
                cursor: pointer;
                border-radius: 5px;
                transition: transform 0.3s ease-in-out;
            }
            .parent_portal #applicantsTable img.app_img {
                border-radius: 50%;
            }
            

            /* Responsive Styles */
            @media screen and (max-width: 768px) {
                .sidebar {
                    transform: translateX(-100%);
                }

                .main-content {
                    margin-left: 0;
                }

                .menu-btn {
                    display: block;
                }

                .sidebar.active {
                    transform: translateX(0);
                }

                .main-content.active {
                    margin-left: 200px;
                }

                .backdrop.active {
                    display: block;
                }
            }
        </style>
    </head>

    <body style="background-image: url('../assets/img/hero-bg-light.webp');">

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <ul class="sidebar-nav">
                <li><a href="#" class="menu-item">Dashboard</a></li>
                <!-- <li><a href="#" class="menu-item">Users</a></li>
                <li><a href="#" class="menu-item">Reports</a></li>
                <li><a href="#" class="menu-item">Settings</a></li> -->
            </ul>
        </div>

        <!-- Backdrop -->
        <div class="backdrop" id="backdrop"></div>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <div class="menu-btn" id="menu-btn">
                <i class="fas fa-bars"></i>
            </div>
            <div class="">
                <h1>Admin Dashboard</h1>
                
                <div class="parent_portal">
                    <fieldset>
                        <legend>Applicant Details:</legend>

                        <table id="applicantsTable" class="table table-bordered display table-responsive">
                            <thead>
                            <tr>
                                <th>SN#</th> 
                                <th class="nowrap">Applicant Name</th> 
                                <th class="nowrap">Fees Paid</th> 
                                <th class="nowrap">Applicant ID / Login ID</th> 
                                <th class="nowrap">Password</th> 
                                <th class="nowrap">Exam Status</th> 
                                <th class="nowrap">Exam Date</th> 
                                <th class="nowrap">Result Date</th> 
                                <th class="nowrap">Result Status</th> 
                                <th class="nowrap">Qualified Rank</th> 
                                <th class="nowrap">Religional Rank</th> 
                                <th class="nowrap">National Rank</th> 
                                <th class="nowrap">International Rank</th> 
                                <th class="nowrap">Category</th>
                                <th class="nowrap">Class</th>
                                <th class="nowrap">School Name</th> 
                                <th class="nowrap">Unique ID</th> 
                                <th>Upload ID</th>
                                <th>Upload Photo</th>
                                <th class="center">Update</th>
                                <th class="center">Report</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php                              
                                $stmt = $conn->prepare("SELECT application_id, applicant_name, category_name, class, aadhar_uid, applicant_password, school_name, id_proof, upload_photo, amount, user_id FROM applicant_master");
                                // $stmt->bindParam(':user_id', $user_id);
                                $stmt->execute();
                                $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (!empty($applicants)) { 
                                    $sn = 1;
                                    foreach ($applicants as $applicant) {
                                        // Decrypt password
                                        $decrypted_password = decrypt_password($applicant['applicant_password'], $encryption_key);
                            ?>
                            
                            <tr data-id="<?= htmlspecialchars($applicant['application_id']); ?>">
                                <td class="center"><?= $sn++; ?></td>
                                <td class="nowrap" id="td_app_name"><?= htmlspecialchars($applicant['applicant_name']); ?></td>
                                <td class="right"><?= '₹' . htmlspecialchars($applicant['amount']); ?></td>
                                <td class="center" id="td_app_id"><?= htmlspecialchars($applicant['application_id']); ?></td>
                                <td class="center"><?= htmlspecialchars($decrypted_password); ?></td>
                                <td class="center">Open</td>
                                <td class="center">TBD</td>
                                <td class="center">TBD</td>
                                <td class="center">TBD</td>
                                <td class="center">TBD</td>
                                <td class="center">TBD</td>
                                <td class="center">TBD</td>
                                <td class="center">TBD</td>
                                <td class="center nowrap"><?= htmlspecialchars($applicant['category_name']); ?></td>
                                <td class="center nowrap" id="td_app_class"><?= htmlspecialchars($applicant['class']); ?></td>
                                <td class="left" id="td_app_school"><?= htmlspecialchars($applicant['school_name']); ?></td>
                                <td><?= htmlspecialchars($applicant['aadhar_uid']); ?></td>

                                <td class="center">
                                    <?php if (!empty($applicant['id_proof'])) { ?>
                                        <img src="uploads/<?= htmlspecialchars($applicant['id_proof']); ?>" alt="ID Proof" loading="lazy"/>
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <?php if (!empty($applicant['upload_photo'])) { ?>
                                        <img src="uploads/<?= htmlspecialchars($applicant['upload_photo']); ?>" class="app_img" alt="Profile Photo" loading="lazy"/>
                                    <?php } ?>
                                </td>
                                
                                <td class="center"><button class="btn edit-btn">Edit</button></td> 
                                <td class="center">
                                    <button type="button" class="btn generate-btn" data-bs-toggle="modal" data-bs-target="#applicantModal" data-id="<?= htmlspecialchars($applicant['application_id']); ?>">Generate</button>
                                </td>
                            </tr>

                            <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="10" class="text-center">No Applicants Available</td></tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                    </fieldset>
                </div>

            </div>
        </div>

        <!-- Edit btn click Model Open -->
        <div class="modal fade" id="editApplicantModal" tabindex="-1" role="dialog" aria-labelledby="editApplicantModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editApplicantModalLabel">Edit Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editApplicantForm">
                        <div class="row">
                            <div class="col-6 col-md-4">
                                <div class="form-group">
                                    <label for="applicantId">Applicant ID</label>
                                    <input type="text" class="form-control" id="applicantId" readonly>
                                </div>
                            </div>
                            <div class="col-6 col-md-8">
                                <div class="form-group">
                                    <label for="applicantName">Applicant Name</label>
                                    <input type="text" class="form-control" id="applicantName" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="className">Class/Others</label>
                            <select name="className" id="className" class="form-control">
                                <option value="" selected disabled>-- Select --</option>
                                <option value="Class 1">Class 1</option>
                                <option value="Class 2">Class 2</option>
                                <option value="Class 3">Class 3</option>
                                <option value="Class 4">Class 4</option>
                                <option value="Class 5">Class 5</option>
                                <option value="Class 6">Class 6</option>
                                <option value="Class 7">Class 7</option>
                                <option value="Class 8">Class 8</option>
                                <option value="Class 9">Class 9</option>
                                <option value="Class 10">Class 10</option>
                                <option value="Class 11">Class 11</option>
                                <option value="Class 12">Class 12</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="schoolName">School Name</label>
                            <input type="text" class="form-control" id="schoolName" placeholder="Enter School Name">
                        </div>
                        <div class="form-group">
                            <label for="uploadIdProof">Upload ID Proof <small>(Max size: 1 MB)</small></label>
                            <input type="file" class="form-control-file" id="uploadIdProof" accept="image/*">
                            <small class="text-danger" id="idProofError" style="display:none;">Only image files are allowed.</small>
                        </div>
                        <div class="form-group">
                            <label for="uploadImage">Upload Image <small>(Max size: 1 MB)</small></label>
                            <input type="file" class="form-control-file" id="uploadImage" accept="image/*">
                            <small class="text-danger" id="imageError" style="display:none;">Only image files are allowed.</small>
                        </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="updateRecordBtn">Update</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Modal Structure -->
        <div id="imageModal" class="modal image-model" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" alt="Large Image" style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Update Address-->
        <div class="modal fade" id="updateAddressModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Address Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0 pt-3">
                        <form id="updateAddressForm">
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" class="form-control" id="address1" name="address1" placeholder="Address Line 1" required>
                            <div class="invalid-feedback">Address is required.</div>
                            <input type="text" class="form-control mt-2" id="address2" name="address2" placeholder="Address Line 2">
                        </div>
                        <div class="form-group">
                            <label for="city">City:</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                            <div class="invalid-feedback">City is required.</div>
                        </div>
                        <div class="form-group">
                            <label for="state">State:</label>
                            <input type="text" class="form-control" id="state" name="state" placeholder="State / Province / Region" required>
                            <div class="invalid-feedback">State is required.</div>
                        </div>
                        <div class="form-group">
                            <label for="pincode">Pincode:</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Postal / Zip Code" required>
                            <div class="invalid-feedback">Pincode is required.</div>
                        </div>
                        <div class="form-group">
                            <label for="country">Country:</label>
                            <input type="text" class="form-control" id="country" name="country" placeholder="Country" required>
                            <div class="invalid-feedback">Country is required.</div>
                        </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="updateAddressBtn" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- model for generate btn -> report -->
        <div class="modal fade" id="applicantModal" tabindex="-1" aria-labelledby="applicantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="applicantModalLabel">Applicant Details - <span id="appName"></span></h5>
                        <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <!-- Applicant Information Section -->
                        <div class="section-header">Personal Information</div>
                        <div class="applicant-section">
                        <div class="info"><label>Applicant ID / Login ID:</label> <span id="appId"></span></div>
                        <div class="info"><label>Unique ID:</label> <span id="uniqueId"></span></div>
                        </div>

                        <!-- Exam Information Section -->
                        <div class="section-header">Exam Information</div>
                        <div class="applicant-section">
                        <div class="info"><label>Exam Date:</label><span id="examDate"></span></div>
                        <div class="info"><label>Exam Status:</label><span class="highlight" id="examStatus"></span></div>
                        <div class="info"><label>Result Date:</label><span id="resultDate"></span></div>
                        <div class="info"><label>Result Status:</label><span class="highlight" id="resultStatus"></span></div>
                        </div>

                        <!-- Rank Information Section -->
                        <div class="section-header">Rank Information</div>
                        <div class="applicant-section">
                        <div class="info"><label>Qualified Rank:</label><span id="qualifiedRank"></span></div>
                        <div class="info"><label>Regional Rank:</label><span id="regionalRank"></span></div>
                        <div class="info"><label>National Rank:</label><span id="nationalRank"></span></div>
                        <div class="info"><label>International Rank:</label><span id="internationalRank"></span></div>
                        </div>

                        <div class="section-header">Essay Topic</div>
                        <div class="info"><label>Topic:</label><span id="essayTopic"></span></div>
                        <div class="info essay"><label>Essay Content:</label><span id="essayContent"></span></div>
                        
                        <div class="applicant-section">
                        <div class="info"><label>Word Count:</label><span id="wordCount"></span></div>
                        <div class="info"><label>Sentence Count:</label><span id="sentenceCount"></span></div>
                        </div>
                        <div class="applicant-section">
                        <div class="info"><label>Essay Start Time:</label><span id="essayStartTime"></span></div>
                        <div class="info"><label>Essay End Time:</label><span id="essayEndTime"></span></div>
                        </div>      
                        <div class="applicant-section">
                        <div class="info"><label>Total Time:</label><span id="essayTime"></span></div>
                        </div>                  
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="printApplicant">Print Report</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optional Bootstrap JS for toggling -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://kit.fontawesome.com/a076d05399.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.getElementById('sidebar');
                const menuBtn = document.getElementById('menu-btn');
                const backdrop = document.getElementById('backdrop');
                const mainContent = document.getElementById('main-content');

                // Toggle sidebar and backdrop on menu button click
                menuBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                    backdrop.classList.toggle('active');
                });

                // Close sidebar when clicking the backdrop
                backdrop.addEventListener('click', function () {
                    sidebar.classList.remove('active');
                    backdrop.classList.remove('active');
                });

                // Close sidebar when clicking a menu item
                document.querySelectorAll('.menu-item').forEach(item => {
                    item.addEventListener('click', function () {
                        sidebar.classList.remove('active');
                        backdrop.classList.remove('active');
                    });
                });
            });
        $(document).ready(function () {
            // Show edit modal and populate fields
            $('.edit-btn').click(function () {
                const row = $(this).closest('tr');
                $('#applicantId').val(row.find('#td_app_id').text());
                $('#applicantName').val(row.find('#td_app_name').text());
                $('#className').val(row.find('#td_app_class').text());
                $('#schoolName').val(row.find('#td_app_school').text());
                // const schoolName = row.find('td:nth-child(15)').text(); // School Name
                
                $('#editApplicantModal').modal('show');
            });
            
            // SchoolName validation: auto-capitalize input
            $('#schoolName').on('input', function () {
                // $(this).val($(this).val().toLowerCase().replace(/\b\w/g, char => char.toUpperCase()));
                
                const value = $(this).val().toLowerCase().replace(/\b\w/g, function(char) {
                return char.toUpperCase();
                });
                $(this).val(value);
            });

            // Function to validate file input
            function validateFile(input, errorId) {
                const file = input.files[0];
                const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const maxFileSize = 1 * 1024 * 1024;   // Maximum file size (1 MB)

                // Check if file exists
                if (file) {
                // Check if the file type is an image
                if (!validImageTypes.includes(file.type)) {
                    $(errorId).text('Only JPEG, PNG, or GIF files are allowed.').show();
                    return false;
                }

                // Check if the file size exceeds the limit
                if (file.size > maxFileSize) {
                    $(errorId).text('File size must be less than 1 MB.').show();
                    return false;
                }

                // Hide error message if validation passes
                $(errorId).hide();
                return true;
                }

                // No file selected, hide error message
                $(errorId).hide();
                return true;
            }

            // Validate files before form submission after button click
            $('#updateRecordBtn').click(function () {
                const isIdProofValid = validateFile($('#uploadIdProof')[0], '#idProofError');
                const isImageValid = validateFile($('#uploadImage')[0], '#imageError');

                // Prevent submission if files are invalid
                if (!isIdProofValid || !isImageValid) {
                return false;
                }

                // Get values from modal inputs
                // const applicantId = $('#applicantId').val();
                // const className = $('#className').val();
                // const schoolName = $('#schoolName').val();
                // const idProofFile = $('#uploadIdProof')[0].files[0]; // ID Proof file
                // const imageFile = $('#uploadImage')[0].files[0]; // Image file

                // const formData = new FormData();
                // formData.append('applicantId', applicantId);
                // formData.append('className', className);
                // formData.append('schoolName', schoolName);
                // if (idProofFile) formData.append('idProofFile', idProofFile);
                // if (imageFile) formData.append('imageFile', imageFile);

                // Get form values and prepare FormData for AJAX request
                const formData = new FormData();
                formData.append('applicantId', $('#applicantId').val());
                formData.append('className', $('#className').val());
                formData.append('schoolName', $('#schoolName').val());
                if ($('#uploadIdProof')[0].files[0]) {
                formData.append('idProofFile', $('#uploadIdProof')[0].files[0]);
                }
                if ($('#uploadImage')[0].files[0]) {
                formData.append('imageFile', $('#uploadImage')[0].files[0]);
                }

                // Send data via AJAX
                $.ajax({
                url: 'updateRecord.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response === 'success') {
                        location.reload();
                    } else {
                        alert('Update failed: ' + response);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Error saving changes: ' + error);
                }
                });

                // Hide the modal
                $('#editApplicantModal').modal('hide');
            });

            // Close modal functionality
            $('.close').click(() => $('#editApplicantModal').modal('hide'));

            // When a user clicks on the profile image
            $('.parent_portal img').click(function() {
                var imageUrl = $(this).attr('src');
                
                $('#modalImage').attr('src', imageUrl);
                $('#imageModal').addClass('show');
                
                setTimeout(function() {
                $('#imageModal').removeClass('show');
                }, 2500);

                $('#imageModal').click(function() {
                $(this).removeClass('show'); // Close modal
                });
            });
            
            $('.generate-btn').click(function () {
                const row = $(this).closest('tr');
                const applicationId = row.data('id'); // Use data attribute for ID

                // Fetch applicant details using AJAX
                $.ajax({
                url: 'fetch_applicant_details.php', // Ensure correct file path
                type: 'GET',
                data: { application_id: applicationId },
                success: function (response) {
                        const applicant = JSON.parse(response);
                        if (applicant.success) {
                            const data = applicant.data;
                            // Populate modal fields
                            $('#appId').text(data.application_id);
                            $('#appName').text(data.applicant_name);
                            $('#uniqueId').text(data.aadhar_uid);
                            $('#examDate').text(data.exam_date);
                            $('#examStatus').text(data.exam_status);
                            $('#resultDate').text(data.result_date);
                            $('#resultStatus').text(data.result_status === 0 ? 'Pass' : 'Pending');
                            $('#qualifiedRank').text(data.qualified_rank);
                            $('#regionalRank').text(data.religional_rank);
                            $('#nationalRank').text(data.national_rank);
                            $('#internationalRank').text(data.international_rank);
                            $('#essayTopic').text(data.essay_topic);
                            $('#essayContent').html(data.essay_description);
                            $('#wordCount').text(data.word_count);
                            $('#sentenceCount').text(data.sentence_count);
                            $('#essayTime').text(data.essay_time);
                            $('#essayStartTime').text(data.essay_start_time);
                            $('#essayEndTime').text(data.essay_end_time);
                            
                            // Show the modal
                            $('#applicantModal').modal('show');
                        } else {
                            alert(applicant.message);
                        }
                },
                error: () => alert('An error occurred while fetching applicant details.')
                });
            });

            // Printing functionality
            $('#printApplicant').on('click', function() {
                window.print(); // Trigger print window
            });
            
            // Handle form submission with validation
            $('#updateAddressBtn').on('click', function(e) {
                e.preventDefault();
                let isValid = true;

                // Validate each required input field
                $('#updateAddressForm input[required]').each(function() {
                if ($(this).val().trim() === '') {
                    $(this).addClass('is-invalid'); // Add red border to invalid fields
                    $(this).next('.invalid-feedback').show(); // Show the error message
                    isValid = false; // Set form validity to false
                } else {
                    $(this).removeClass('is-invalid').next('.invalid-feedback').hide();
                }
                });
                
                // Validate pincode (adjust based on country format)
                let pincode = $('#pincode').val().trim();
                if (!/^\d{5,6}$/.test(pincode)) {
                $('#pincode').addClass('is-invalid');
                $('#pincode').next('.invalid-feedback').text('Invalid Pincode. Must be 5-6 digits.');
                isValid = false;
                }

                if (isValid) {
                // Get form data
                var formData = {
                    user_id: "<?= $uniqueId; ?>", // Correct PHP variable to get Parent ID
                    address1: $('#address1').val(),
                    address2: $('#address2').val(),
                    city: $('#city').val(),
                    state: $('#state').val(),
                    pincode: $('#pincode').val(),
                    country: $('#country').val()
                };

                // Simulate successful AJAX response
                $.ajax({
                    url: 'update_address.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.trim() === 'success') {
                            $('#message').html('<div class="alert alert-success">Address updated successfully!</div>');
                            $('#updateAddressForm')[0].reset();
                            location.reload();
                            $('#updateAddressModal').modal('hide');                                                
                        } else {
                            $('#message').html('<div class="alert alert-danger">Error updating address. Please try again.</div>');
                        }
                    },
                    error: function() {
                        $('#message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
                }
            });
            
            // Remove validation messages on input focus
            $('#updateAddressForm input').on('focus', function() {
                $(this).removeClass('is-invalid').next('.invalid-feedback').hide();
            });
        });
        </script>
    </body>
</html>

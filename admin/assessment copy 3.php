<?php
   session_start();
   include '../config.php';

   if(!isset($_SESSION['name'])) {
      header("location:../login");
   }

   //site base name
   $p_title = basename($_SERVER['PHP_SELF'],".php");
      
   $user_id = $_SESSION['user_id'] ?? '';
   $name = htmlspecialchars($_SESSION['name']) ?? '';
   $uniqueId = htmlspecialchars($_SESSION['uniqueId']) ?? '';
   
   $stmt = $conn->prepare("
      SELECT application_id, class, school_name, id_proof, upload_photo 
      FROM applicant_master 
      WHERE application_id = :uniqueId AND user_id = :user_id
   ");
   $stmt->execute([':uniqueId' => $uniqueId, ':user_id' => $user_id]);
   $applicant_details = $stmt->fetch(PDO::FETCH_ASSOC);

   $application_id = $applicant_details['application_id'] ?? '';
   $class = $applicant_details['class'] ?? '';
   $school = $applicant_details['school_name'] ?? '';
   $id_proof = $applicant_details['id_proof'] ?? '';
   $upload_photo = $applicant_details['upload_photo'] ?? '';

   // Remove "Class" followed by any space or hyphen
   $onlyNumber = preg_replace("/class[\s-]*/i", "", $class);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Assessment Portal</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- textarea  -->
   <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

   <!-- custom css file link  -->
   <link rel="stylesheet" type="text/css" href="../admin/css/style.css?v=<?= rand(1,4) . '.' . rand(1,99); ?>" />

   <style>
      .dashboard {
         padding: 5px;
         max-width: 100%;
         margin: 0 auto;
      }

      .has-sidebar main {
         padding-left: 30rem;
      }

      .no-sidebar main {
         padding-left: 0;
      }

      .preview-content-confirm {
         display: flex !important;
         display: -ms-flexbox !important;
         justify-content: space-between;
         margin-top: 6px;     
      }
      .radio-label:has(input[type="radio"]) {
         font-size: 14px;
         margin-bottom: 3px;
         cursor: pointer;
      }
      
      input[type="radio"]:not(:checked) + span {
         color: gray;
      }

      .topic {
         font-weight: bold;
         font-size: 15px;
      }
      #editTopicButton {
         display: none;
         margin-top: -3px;
         margin-left: 5px;
      }
      #radio-buttons {
         padding-top: 5px;
      }
      
      #preview-topic { 
         font-size: 14px;
         color: var(--main-color);
         margin: 5px 0 8px 0;         
         font-weight: bold;
      }
      #preview-button {
         margin-top: 8px;
         display: flex;
         justify-content: center;
      }
      #confirm-submit {
         color: #fff;
         display: none;
      }

      .profile-pic {
         height: 35px;
         width: 35px;
         border-radius: 50%;
         cursor: pointer;
      }

      .applicant-name {
         color: #fff;
         font-size: 15px;
         font-weight: bold;
         margin-left: 4px;
      }

      .extra-info {
         display: none;
         position: absolute;
         top: 55px;         
         background-color: #fff;
         padding: 8px;
         min-width: 95%;
         border-radius: 8px;
         box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);         
         word-break: break-word;         
      }

      .profile-container:hover .extra-info {
         display: block;
      }
   </style>
</head>

<body style="background-image: url('../assets/img/hero-bg-light.webp');">
   <!-- Top header -->   
   <nav class="navbar nav-1 pl-3">
      <div class="profile-container">
         <?php if(!$isParent && !empty($upload_photo)) { ?>
            <img src="../admin/uploads/<?= htmlspecialchars($upload_photo); ?>" class="profile-pic mr-2" alt="Profile Photo" loading="lazy"/>
         <?php } ?>
         <span class="applicant-name"><?= $name; ?></span>
         <div class="extra-info">
            <span><?= $class ? '<strong>Class: </strong>' . htmlspecialchars($onlyNumber) : ''; ?></span><br>
            <span><?= $school ? '<strong>School: </strong>' . htmlspecialchars($school) : ''; ?></span>
         </div>
      </div>
      
      <div class="d-flex align-items-center">
         <!-- Timer beside the logout & Logout button with confirmation -->
         <div class="timer font-weight-bold lead text-white" id="">02:00:00</div>
         
         <a href="components/logout.php" title="logout" id="logoutBtn" onclick="return confirm('Logout from this website?')">
            <i class="fas fa-right-from-bracket"></i>
         </a>
      </div>
   </nav>
   
   <!-- dashboard section starts  -->
   <section class="dashboard">
      <main>
         <span class="topic">Select a topic:</span>
         <button id="editTopicButton" class="btn" onclick="enableRadioButtons()">Edit Topic</button>
         
         <div id="radio-buttons">
            <?php
               $category_id = $_SESSION['category_id'] ?? '';
               if ($category_id) {
                  try {                                             
                     $stmt = $conn->prepare("SELECT category_id, category_name, sub_category FROM category_master WHERE category_id = :category_id");
                     $stmt->bindParam(':category_id', $category_id);
                     $stmt->execute();

                     if ($stmt->rowCount() > 0) {
                        echo "<form id='radio-form'>";
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                           echo "<label class='radio-label'>
                              <input type='radio' name='input_radio' class='topic-radio' value='" . htmlspecialchars($row["sub_category"]) . "' onchange='startTimer()'><span>" . htmlspecialchars($row["sub_category"]) . "</span>
                           </label><br>";
                        }
                        echo "</form>";
                     } else {
                        echo "No records found";
                     }
                  } catch (PDOException $e) {
                     echo "Error: Occured";
                  }
               }
            ?>
         </div>
         
         <div class="text-box-section">
            <!-- Toolbar for Quill.js -->
            <div id="toolbar">
               <button class="ql-bold">B</button>
               <button class="ql-italic">I</button>
               <button class="ql-underline">U</button>
               <button class="ql-list" value="ordered">Ordered List</button>
               <button class="ql-list" value="bullet">Bullet List</button>
               <button class="ql-code-block">Code Block</button>
            </div>

            <!-- Create the editor container -->
            <div id="editor"></div>
            
            <div class="counter">
               <div class="count-display">
                  <span id="wordCount">Words: 0</span>
                  <span id="sentenceCount">Sentences: 0</span>
               </div>
               <div id="error-message"></div>
            </div>

            <!-- Preview and Save buttons -->
            <div class="button-section">
               <div class="left">
                  <button id="preview" class="btn" type="button">Preview</button>
               </div>
               <div class="right">
                  <span class="mr-3" style="font-weight: bold; font-size: 13px;"><strong>Auto Save: ON</strong></span>
                  <button id="save" class="btn" type="submit">Submit</button>
               </div>
            </div>
            
            <!-- Modal for Preview -->
            <div id="previewModel" class="modal" style="display:none;">
               <div class="modal-content">
                  <div class="flex">
                     <h4>Preview</h4>
                     <span class="close" id="close">
                        <i class="fas fa-times"></i>
                     </span>
                  </div>

                  <div id="preview-topic"></div>
                  <div id="preview-content"></div>
                  <div class="preview-content-confirm">
                     <div class="d-flex">
                        <h5 id="word" class="mr-3"></h5> | 
                        <h5 id="sentence" class="ml-3"></h5>
                     </div>
                     <div id="preview-button justify-content-end">
                        <button id="confirm-submit" class="btn">Confirm & Proceed</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>         
      </main>
   </section>
   <!-- dashboard section ends -->
 
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>   
   <script>       
      
      // Track if the Edit Topic button should be displayed
      let isFirstSelection = true;
      let previousSelection = null;

      // Function to enable radio buttons and set up the change event
      function enableRadioButtons() {
         const radioButtons = document.querySelectorAll('input[name="input_radio"]');
         
         radioButtons.forEach(radio => {
            radio.disabled = false; // Enable radio buttons
            
            radio.addEventListener('change', function() {
               const selectedValue = this.value;
               
               // Show confirmation only if the selection has changed
               if (previousSelection !== selectedValue) {
                  // const confirmation = confirm(`Do you want to change??`);
                  
                  if (confirmation) {
                     quill.setContents([]); // Clear Quill editor content
                     previousSelection = selectedValue; // Update previous selection
                     localStorage.clear(); // Clear localStorage when confirmed
                  } else {
                     // Revert to the previous radio button if the user cancels
                     if (previousSelection !== null) {
                        document.querySelector(`input[name="input_radio"][value="${previousSelection}"]`).checked = true;
                     } else {
                        this.checked = false; // No previous selection, just uncheck the current button
                     }
                  }
               }
            });
         });
      }

      // Call the function to enable radio buttons
      enableRadioButtons();

      
   </script>
   <script>
      $(document).ready(function () {
         // Initialize Quill.js
         // var quill = new Quill('#editor', { modules: { toolbar: '#toolbar' }, theme: 'snow', formats: ['bold', 'italic', 'underline', 'list', 'code-block'] });
         const quill = new Quill('#editor', { modules: { toolbar: '#toolbar' }, theme: 'snow', formats: ['bold', 'italic', 'underline', 'list', 'code-block'] });
         quill.enable(false); // Disable editor initially
         $('#save').prop('disabled', true); // Disable submit initially

         // Prevent copy, cut, and paste events
         // $.each(['copy', 'cut', 'paste'], function(_, event) {
         //    $(quill.root).on(event, function(e) {
         //       e.preventDefault(); // Prevent clipboard actions
         //    });
         // });
         
         const modal = $('#previewModel'),
            radioButtons = $('input[name="input_radio"]'),
            previewButton = $('#preview'),
            submitButton = $('#save'),
            confirmSubmitButton = $('#confirm-submit'),
            previewContent = $('#preview-content'),
            previewTopic = $('#preview-topic'),
         timerElement = $('#timer');

         let autoSaveInterval, selectedTopic = false;
         let isStartTimeUpdated = false; // Flag to track if start time is updated
         const totalEssayTime = 2 * 60 * 60; // 2 hours in seconds       
         
      
         // Restore saved data (editor content, topic, timer)
         const savedTopic = localStorage.getItem('selectedTopic');
         const savedTime = localStorage.getItem('timerValue') || totalEssayTime;
         const savedContent = localStorage.getItem('draftContent');
         if (savedContent) quill.root.innerHTML = savedContent; // Restore editor content

         if (savedTopic) {
            // $(`input[name="input_radio"][value="${savedTopic}"]`).prop('checked', true).parent().addClass('selected');
            $(`input[name="input_radio"][value="${savedTopic}"]`).prop('checked', true).prop('disabled', true);
            quill.enable(true);
            submitButton.prop('disabled', false);
            startTimer(savedTime); // Start timer from saved time
            // selectedTopic = true;
         
            // Disable other radio buttons
            // radioButtons.not(`[value="${savedTopic}"]`).prop('disabled', true);
         }
         if (savedTime) timerElement.text(savedTime); // Restore timer

-------------------------------------------------------------------
 // Radio button selection
 $('input[name="input_radio"]').change(function () {
      const selectedTopic = $(this).val();
      if (!savedTopic || (savedTopic && confirm("Do you want to change the topic?"))) {
         localStorage.setItem('selectedTopic', selectedTopic);
         quill.enable(true);
         startTimer(savedTime); // Start timer from saved time
         autoSaveInterval = setInterval(autoSaveContent, 5000);
         $(this).prop('disabled', true);
      } else {
         $(this).prop('checked', false);
      }
   });
--------------------------------------------------------------------

         function handleFirstSelection(selectedValue) {
               isFirstSelection = false;
               previousSelection = selectedValue;
               $('.timer').attr('id', 'timer');
               $('#editTopicButton').show();
               quill.enable(true);
               localStorage.setItem('selectedTopic', selectedValue);
               
               // radioButtons.not(this).prop('disabled', true);
               // selectedTopic = true;

               radioButtons.prop('disabled', true).not(this).prop('disabled', false);
               clearInterval(autoSaveInterval);
               autoSaveInterval = setInterval(autoSaveContent, 5000);
         }

         function handleTopicChange(selectedValue) {
               previousSelection = selectedValue;
               localStorage.clear();
               $('#editTopicButton').show();
               quill.enable(true);
               localStorage.setItem('selectedTopic', selectedValue);
               radioButtons.prop('disabled', true).not(this).prop('disabled', false);
               // selectedTopic = true;
               clearInterval(autoSaveInterval);
               autoSaveInterval = setInterval(autoSaveContent, 5000);
         }

         radioButtons.change(function () {
            const selectedValue = $(this).val();
            
            // If this is the first selection, no alert is shown
            if (isFirstSelection) {
               handleFirstSelection(selectedValue);
               isFirstSelection = false;  // Set flag to false after the first selection
               previousSelection = selectedValue;
            } else if (previousSelection !== selectedValue) {
               if (confirm(`Do you want to change the topic?`)) {
                  handleTopicChange(selectedValue);
                  //   $(this).prop('checked', false);
                  //   previousSelection ? $(`input[value="${previousSelection}"]`).prop('checked', true) : $(this).prop('checked', false);
               } else {
                  previousSelection ? $(`input[value="${previousSelection}"]`).prop('checked', true) : $(this).prop('checked', false);
               }
            }
         });

         
         if (!selectedTopic) {
            $('#editTopicButton').hide();
         } else {
            $('#editTopicButton').show();
         }

         // Auto-save content and timer to localStorage
         function autoSaveContent() {
            localStorage.setItem('draftContent', quill.root.innerHTML);
            localStorage.setItem('timerValue', timerElement.text());
         }

         // Submit functionality
         submitButton.on('click', function () {
            const selectedTopic = $('input[name="input_radio"]:checked').val();
            if (!selectedTopic) return alert('Please select a topic.');
            previewTopic.text('Topic [ ' + selectedTopic + ' ]');
            previewContent.html(quill.root.innerHTML);
            confirmSubmitButton.show();
            modal.show();
            
            // Get the word and sentence counts by calling Count()
            const counts = Count();
            $('#word').text(`Words: ${counts.wordCount}`);
            $('#sentence').text(`Sentences: ${counts.sentenceCount}`);
         });

         // Close modal functionality
         $('#close').on('click', function () {
            modal.hide();
         });

         // Preview button click event
         previewButton.on('click', function () {
            const selectedTopic = $('input[name="input_radio"]:checked');
            modal.show();
            
            if (selectedTopic.length) {
               previewTopic.html('Topic [ ' + selectedTopic.val() + ' ]').css('color', '');
               confirmSubmitButton.hide();
               // updateCounts();

               // Get the word and sentence counts by calling Count()
               const counts = Count();
               $('#word').text(`Words: ${counts.wordCount}`);
               $('#sentence').text(`Sentences: ${counts.sentenceCount}`);

               previewContent.html(quill.root.innerHTML);
            } else {
               previewTopic.text('--Please Select a Topic--').css('color','#f00');
            }
         });  



         function getTimeTaken() {
            // let currentTime = $('#timer').text();
            const currentTime = timerElement.text();
            const [hours, minutes, seconds] = currentTime.split(':').map(Number);
            // let remainingTime = (hours * 3600) + (minutes * 60) + seconds;
            const remainingTime = (hours * 3600) + (minutes * 60) + seconds;

            

            // Calculate time taken
            // let timeTaken = totalEssayTime - remainingTime;
            
            // // Convert timeTaken (in seconds) back to HH:MM:SS format
            // const takenHours = String(Math.floor(timeTaken / 3600)).padStart(2, '0');
            // const takenMinutes = String(Math.floor((timeTaken % 3600) / 60)).padStart(2, '0');
            // const takenSeconds = String(timeTaken % 60).padStart(2, '0');

            // return `${takenHours}:${takenMinutes}:${takenSeconds}`;

            return totalEssayTime - remainingTime; // Total time minus remaining time
        }


         // Confirm Submit button click event
         confirmSubmitButton.on('click', function () {            
            const confirmSubmit = confirm("Make sure everything is correct. You won't be able to change the details after submission.");

            const counts = Count();
            // const timeTaken = getTimeTaken();

            if (confirmSubmit) {
               $.ajax({
                  url: 'save_essay.php',
                  type: 'POST',
                  contentType: 'application/json',
                  data: JSON.stringify({
                     application_id: "<?= $application_id ?>", // Correct PHP variable to get application ID
                     topic: $('input[name="input_radio"]:checked').val(),
                     content: quill.root.innerHTML,
                     time_taken: getTimeTaken(),
                     // time_taken: timeTaken,
                     word_count: counts.wordCount, // Include word count
                     sentence_count: counts.sentenceCount // Include sentence count
                  }),
                  success: (response) => {
                     if (response.success) {
                        modal.hide(); // Close modal                           
                        clearLocalData();
                        alert('Submitted successfully!');
                        window.location.href = 'studentDashboard';
                     } else alert('Error: ' + response.message);
                  },
                  error: () => alert('An error occurred while submitting the essay.')
               });
            }
         });

         // Auto Save when timer ends
         let autoSaveTriggered = false; // Flag to avoid multiple auto-saves
         

         // Function to check word count and auto-save if necessary
         function checkAutoSave() {
            const counts = Count(); // Get word and sentence counts
            const timeTaken = getTimeTaken(); // Save essay time
            
            // if (!autoSaveTriggered) {
               // autoSaveTriggered = true; // Prevent multiple auto-saves
               
               const content = quill.root.innerHTML;
               // Perform additional validation if needed, like checking for empty content
               if (content.trim() === '<p><br></p>' || content.trim() === '') {
                  alert('Editor is empty! Please enter some content.');
                  return;
               }

               // Auto-save the essay
               $.ajax({
                     url: 'save_essay.php',
                     type: 'POST',
                     contentType: 'application/json',
                     data: JSON.stringify({
                        application_id: "<?= $application_id ?>", // Correct PHP variable to get application ID
                        topic: $('input[name="input_radio"]:checked').val(),
                        content: content,
                        time_taken: timeTaken,
                        word_count: counts.wordCount, // Include word count
                        sentence_count: counts.sentenceCount // Include sentence count
                     }),
                     success: handleAutoSaveResponse,
                     error: () => alert('An error occurred while auto-saving the essay.')
                     // success: (response) => {
                     //    if (response.success) {
                     //       modal.hide(); // Close modal                           
                     //       clearLocalData();
                     //       // alert('Auto-saved successfully!');
                     //       window.location.href = 'studentDashboard'; // Redirect to dashboard
                     //    } else {
                     //       alert('Error: ' + response.message);
                     //    }
                     // },
                     // error: () => {
                     //    alert('An error occurred while auto-saving the essay.');
                     // }
               });
            // }
         }

         function handleAutoSaveResponse(response) {
            if (response.success) {
                modal.hide();
                clearLocalData();
                     //       // alert('Auto-saved successfully!');
                     //       window.location.href = 'studentDashboard'; // Redirect to dashboard
                // Optionally notify success
            } else {
                alert('Error: ' + response.message);
            }
        }


        function Count() {
            const text = quill.getText().trim();
            const wordCount = text ? text.split(/\s+/).length : 0;

            const sentences = text.split(/[.!?]+/).filter(Boolean);
            const sentenceCount = sentences.reduce((count, sentence) => {
                const trimmedSentence = sentence.trim();
                return count + (trimmedSentence.split(/\s+/).length >= 3 ? 1 : 0);
            }, 0);

            return { wordCount, sentenceCount };
        }

         // Clear localStorage after successful submission
         function clearLocalData() {
            quill.setContents([]);
            localStorage.clear();
            location.reload();
         }

         // function Count() {
         //    const text = quill.getText().trim();
         //    const wordCount = text ? text.split(/\s+/).length : 0;

         //    // Count sentences with more validation
         //    let sentenceCount = 0;
         //    if (text) {
         //       const sentences = text.split(/[.!?]+/).filter(Boolean);

         //       // Apply additional validation to sentences
         //       sentences.forEach(sentence => {
         //             const trimmedSentence = sentence.trim();

         //             // Check for proper capitalization and punctuation
         //             const startsWithCapital = /^[A-Z]/.test(trimmedSentence);
         //             const endsWithPunctuation = /[.!?]$/.test(trimmedSentence);

         //             // Check if the sentence is valid (e.g., has more than 3 words)
         //             if (trimmedSentence.split(/\s+/).length >= 3) {
         //                sentenceCount++;
         //             }
         //       });
         //    }

         //    // Return both word and sentence counts
         //    return {
         //       wordCount: wordCount,
         //       sentenceCount: sentenceCount,
         //    };
         // }

         // Text change handler for auto-saving and validation
         quill.on('text-change', function() {
            
            autoSaveContent();
            updateCount(); // Update counts on text change
            
            if (!isStartTimeUpdated) {
               $.ajax({
                  url: 'update_start_time.php', // PHP file to update the start time
                  type: 'POST',
                  contentType: 'application/json',
                  data: JSON.stringify({ 
                     application_id: "<?= $application_id ?>"
                     }),
                  success: (response) => {
                     isStartTimeUpdated = true;
                  },
                  error: () => alert('An error occurred while updating the start time.')
               });
            }
         });

         function updateCount() {
            // Get the word and sentence counts by calling Count()
            const counts = Count();

            // Update the UI with the word and sentence counts
            $('#wordCount').text(`Words: ${counts.wordCount}`);
            $('#sentenceCount').text(`Sentences: ${counts.sentenceCount}`);

            // Validate word count range (between 300 and 1000 words)
            const isInvalidLength = counts.wordCount < 300 || counts.wordCount > 1000;
            submitButton.prop('disabled', isInvalidLength); // Disable submit button if invalid
            $('#error-message').text(isInvalidLength ? 'Essay length: Min 300 and Max 1000 Words' : '');
         }


         // Timer functionality
         // let startTime = savedTime ? parseTime(savedTime) : new Date(); // Continue from saved time if available

         // const timerInterval = setInterval(updateTimer, 1000);
         let timerInterval = setInterval(updateTimer, 1000);
         let startTime = new Date();
         if (savedTime) {
            startTime = parseTime(savedTime);
         }

         function updateTimer() {
            const now = new Date(),
               elapsed = Math.floor((now - startTime) / 1000);
            
            // Remaining time logic
            const remaining = totalEssayTime - elapsed;
            const hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const seconds = String(remaining % 60).padStart(2, '0');

            // Display the remaining time
            timerElement.text(`${hours}:${minutes}:${seconds}`);

            // If time limit is reached, disable the editor
            if (remaining <= 0) {
               quill.disable(true); // Disable the text editor
               clearInterval(timerInterval); // Stop the timer
               alert("Time's up! Your writing session has ended.");
               clearLocalData(); // Clear local data (if applicable)
            }

            if (remaining <= 2) {
               // Monitor word count and timer
               setInterval(checkAutoSave, 1000); // Check every second
            }
         }

         // Parse the saved time if resuming from where the user left
         function parseTime(timeString) {
            const [hours, minutes, seconds] = timeString.split(':').map(Number);
            const now = new Date();
            return new Date(now.getTime() - ((hours * 3600) + (minutes * 60) + seconds) * 1000);
         }

      });
   </script>
<script>
// Timer variables
let timer;
let timeLeft = 7200; // 2 hours in seconds (2 * 60 * 60)

// Function to start the timer
function startTimer() {
   if (!timer) { // Start only if not already started
      timer = setInterval(() => {
         if (timeLeft <= 0) {
            clearInterval(timer);
            alert("Time is up!");
            // Save the essay automatically
            autoSaveEssay();
         } else {
            timeLeft--;
            updateTimerDisplay();
         }
      }, 1000); // Update every second
   }
}

// Function to update the timer display
function updateTimerDisplay() {
   const hours = Math.floor(timeLeft / 3600);
   const minutes = Math.floor((timeLeft % 3600) / 60);
   const seconds = timeLeft % 60;
   document.getElementById("timer").innerText = 
      String(hours).padStart(2, '0') + ':' + 
      String(minutes).padStart(2, '0') + ':' + 
      String(seconds).padStart(2, '0');
}

// Function to automatically save the essay
function autoSaveEssay() {
   // Get the content from the Quill editor
   const editorContent = document.getElementById("editor").innerHTML;
   
   // Check word count for auto-save
   const wordCount = getWordCount(editorContent);
   if (wordCount >= 300) {
      // Save logic goes here (e.g., AJAX request to save data)
      console.log("Auto-saving essay with word count:", wordCount);
      // Implement your AJAX request to save the essay
   }
}

// Function to get word count
function getWordCount(content) {
   const text = content.replace(/<[^>]*>/g, ''); // Remove HTML tags
   const words = text.trim().split(/\s+/);
   return words.length;
}
</script>

   <!-- custom js file link  -->
   <script src="../admin/js/script.js"></script>
</body>
</html>
<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] == 'signout') {
    session_unset(); 
    session_destroy();
    header('Location: index.php'); 
    exit();
}

$logged_in = false;
$role = '';

if (isset($_SESSION['username'])) {
    $logged_in = true;
    $role = $_SESSION['role']; 
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>PetPals | Home </title>
        <link rel="stylesheet" href="../css/style.css"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    </head>
    <body>
        <div id="home">
            
            <video autoplay muted loop>
                <source src="../vids/main.mp4" type="video/mp4">
            </video>
            
            <nav>
                <ul>
                    <div class="nav-left">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#services">Our Services</a></li>
                    <li><a href="#contact">Contact us</a></li>
                    </div>
                    <div class="nav-center">
                        <li><img src="../imgs/logo.png" alt="logo"></li>
                    </div>
                    <div class="nav-right">
                    <?php if ($logged_in): ?>
                            <li><a href=profile.php>Profile</a></li>
                            <li><a href="?action=signout">SignOut</a></li>
                        <?php else: ?>
                            <li><a href="signin.php">SignIn</a></li>
                            <li><a href="signup.php">SignUp</a></li>
                        <?php endif; ?>
                    </div>
                </ul>
            </nav>
        </div>

        <div class="donate">
            <h1>WE NEED YOUR HELP</h1>
            <div class="info">
                <div class="pics">
                    <div class="slide">
                    <img src="../imgs/dog.jpg" alt="Slide 1">
                    </div>
                    <div class="slide">
                    <img src="../imgs/cat.jpg" alt="Slide 2">
                    </div>
                    <div class="slide">
                    <img src="../imgs/rabbit.jpg" alt="Slide 3">
                    </div>
                    <div class="slide">
                    <img src="../imgs/pig.jpg" alt="Slide 4">
                    </div> 
                </div>
                <div class="content">
                    <h2>You can make a difference right now</h2>
                    <p>Generosity becomes you. Together we can save lives. </p>
                    <br>
                    <p><b>Donate us at : </b></p>
                    <p>PetPals</p>
                    <p>123456789</p>
                    <p>Trust Bank</p>
                    <p>Colombo</p>
                </div>
            </div>
            <div class="paws">
                <img src="../imgs/paws1.png">
                <img src="../imgs/paws2.png">
            </div>
        </div>

        <div id="about">
            <h2>ABOUT US</h2>
            <p>At PetPals, we believe that every pet deserves a loving home  and a chance to find their <b>forever family</b> . Our journey began with a simple goal: to bridge the gap between abandoned animals and compassionate humans. Every day, we are driven by the love we have for animals and the hope that we can make a difference, one paw at a time.
            <br>
            <br>
            Our dedicated team works tirelessly to rescue, rehabilitate, and rehome pets in need, giving them a second chance at life. We understand that pets aren't just animals—they are loyal companions, family members, and lifelong friends. For us, it's not just about finding homes, but about creating lifelong bonds between pets and their new families.
            <br>
            <br>
            We are more than just an adoption service; we are a community united by love, care, and commitment. Through our efforts, we strive to give every pet a voice, a warm place to sleep, and a future filled with joy. Together, we can change their world and create happy endings for both pets and the families who welcome them into their hearts. At PetPals, <b>we're here to make sure every whisker finds its way home.</b>
            </p>
            <img src="../imgs/main.png">
            
        </div>
        
        <div id="services">
            <h2>OUR SERVICES</h2>
            <div class="services_container">
                <div class="services">
                    <a href="adopt.php">
                        <h3>Pet Adoption</h3>   
                        <p>Our core mission is to help animals find their forever homes. We carefully match pets with families based on compatibility, ensuring that each adoption is a perfect fit for both the pet and their new family. Our team provides detailed profiles of all our pets, including their needs, personalities, and histories, so you can make an informed decision.</p>
                    </a>
                    
                </div>
                <div class="services">
                    <a href="rescue.php">
                        <h3>Rescue and Rehabilitation</h3>   
                        <p>Many of our animals come from difficult backgrounds, and we believe every pet deserves a second chance. Our rescue team works around the clock to save animals from harmful situations, and our rehabilitation experts provide the care, love, and patience they need to heal—both physically and emotionally.</p>
                    </a>
                </div>
                <div class="services">
                    <a href="volunteering.php">
                        <h3>Volunteering</h3>   
                        <p>We welcome animal lovers to join our team of volunteers, whether it’s spending time with the pets, helping with events, or lending a hand in the daily operations. Your time and energy can make a world of difference in the lives of these animals, and there are countless ways to get involved.</p>     
                    </a>
                  
                </div>
                <div class="services">
                    <a href="education.php">
                        <h3>Pet Education and Resources</h3>    
                        <p>We provide ongoing support to adopters with resources on pet care, training tips, health advice, and more. Our goal is to help every new pet parent navigate the joys and challenges of owning a pet, ensuring that your bond with your furry companion continues to grow stronger.</p>
                    </a>
                </div>
            </div>
        </div>

        <div class="contact_heading"><h2>CONTACT US</h2>
            <div id="contact">
                <div><p>Galle Road, Bambalapitiya</p>
                    <address><a href="mailto:amapiumiranasinghe@gmail.com">petpals@gmail.com</a></address>
                    <p>Phone:011 2255441</p>
                </div>
                <div>
                    <ul class="social-links">
                        <li><a href="https://web.facebook.com" title="Facebook"><i class="fab fa-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com" title="Instagram"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
                <div>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3968.0766999772504!2d80.38038777509355!3d5.984177094000772!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae16b4b51bcf019%3A0x4446a95e6f8c1d3a!2sAnimal%20SOS%20Sri%20Lanka%20-%20Sanctuary!5e0!3m2!1sen!2slk!4v1726440255903!5m2!1sen!2slk" width="100%" height="100%" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
        <div class="footer"> 
            <p>&copy; 2024 PetPals. All Rights Reserved.</p>
        </div>  
        <script>
        let slideIndex = 0;

        function showSlides() {
            let slides = document.getElementsByClassName("slide");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  // Hide all slides
            }
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1;  // Reset to first slide
            }
            slides[slideIndex - 1].style.display = "block";  // Show the current slide
            setTimeout(showSlides, 3000);  // Change slide every 3 seconds
        }

        showSlides();  // Initialize slideshow
        </script>
    </body>
</html>
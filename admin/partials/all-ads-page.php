<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!--Here is the sub menu page to create new ads-->


<!DOCTYPE html>
<html lang="en">
<head>
<script>
function displaycheck() {
        if (document.getElementById('iffeatured').checked) {
           document.getElementById('biz').style.display = 'block';
        } else {
           document.getElementById('biz').style.display = 'none';
        }
    }
</script>
</head>
<body>

<p><strong style="font-size:23px;">Create a new ad</strong> <a href="https://www.youtube.com">&nbsp;<button style="padding:5px; font-size:15px;">View All Ads</button> </a></p>

<div class="form-wrapper">
    <form action="">
    <br><p style="font-size:17px;"> Enter the ad title:
    <input type="text" name="ad-title" placeholder=" e.g My Company Ad" size="75">
    </p> <br>
     
    <p> <strong style="font-size:17px;">Select the ad type</strong></p>
   
    <label for="choice-1">
    <input type="radio" id="choice-1" name="ad type" value="Image-ad" checked>Image Ad <span>For images of several formats</span>
    </label>

    <label for="choice-2">
    <br><input type="radio" id="choice-2" name="ad type" value="video-ad">Video Ad <span>Best for youtubers to gain views and subscribers</span>
    </label>

    <label for="choice-3">
    <br><input type="radio" id="choice-3" name="ad type" value="rich-content">Rich content <span>Text editor with options for image and video upload</span> 
    </label>
    
</form>
</div>
    

    <style>
        
        .form-wrapper span{
          visibility: hidden;
        }
        .form-wrapper form label {
            position: relative;
            display: block;
            width: 100%; 
        }
       
        .form-wrapper form label input[type="radio"]:checked + span {
            position: absolute;
            display: inline-block;
            text-align: center;
            left:7%;
           justify-content: center;
            margin: 0 0 0 20px;
            padding: 8px 15px;
            white-space: nowrap;
            color: white;
            background-color: rgb(65, 65, 65);
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(34,34,34,0.2);
            clear: both;
            visibility: visible;
            transform: translateX(0px);
            transition: transform 200ms ease;
        }
        .form-wrapper form label input[type="radio"]:checked + span:before{
            position: absolute;
            content:"";
            top:4%;
            left:-1px;
            border-top: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid rgba(34,34,34,0.9);
            transform: rotate(45deg);
        }
        .number {
            display:none;
        }

    </style>
 <br>

 <label for="ad-category" style="font-size:17px;">Select Ad Category:</label>

<select name="ad-category" id="ad-category">
  <option value="tech">Tech Services</option>
  <option value="job">Job vacancy</option>
  <option value="house">Real Estate</option>
  <option value="car">Automobile</option>
  <option value="food">Food/Restaurant</option>
  <option value="school">School/Education</option>
  <option value="shop">Clothes & Shoes</option>
  <option value="beauty">Beauty & Saloon</option>
  <option value="other">Other</option>
</select>

    <br><br>
    
    <label for="ad-duration" style="font-size:17px;">Select Ad Duration:</label>

<select name="ad-duration" id="ad-duration">
  <option value="lifetime">Lifetime</option>
  <option value="one-week">One Week</option>
  <option value="two-weeks">Two Weeks</option>
  <option value="one-month">One Month</option>
</select>
<br><br>

    <p> <span style="font-size:17px;"> Choose Display Location: </span> &nbsp;&nbsp;<input type="radio" onclick="javascript:displaycheck();" name="ad-display" value="activity" id="notfeatured" checked>Activity Feed Only &nbsp;&nbsp;<input type="radio" onclick="javascript:displaycheck();" name="ad-display" value="featured" id="iffeatured">Activity Feed & Featured Ads Page </p>

<div class="number" id="biz" ><span style="font-size:17px;">WhatsApp Business Number: &nbsp;</span><input type="text" placeholder="e.g +2348167534572" size="35"></div>
<br>
<label for="currency" style="font-size:17px;">Choose Currency:</label>

<select name="currency" id="currency">
  <option value="dollars">USD</option>
  <option value="pounds">GBP</option>
  <option value="euro">EUR</option>
  <option value="cfa">CFA</option>
  <option value="xaf">XAF</option>
  <option value="ngn">NGN</option>
</select>


</body>

</html>
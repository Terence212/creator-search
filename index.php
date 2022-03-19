<?php
include('../header/header.php');
?>

<body>

<div class="project-desc">
This is a simple API integration with Youtube to search content creators by name and return either their most recent video or their current live stream if they are live.
This page was built with Javascript, Php, HTML, Css.
</div>
<h1>Content Creator Search</h1>

<div class="form">
    <div id="platform">
    <!-- <input type="radio" id="twitch" value="twitch" name="platform">
    <label for="twitch">Twitch</label> -->
    <br>
    <input type="radio" id="youtube" value="youtube" name="platform" checked>
    <label for="youtube">Youtube</label>
    <br>
    </div>
    <label for="channelName">Channel Name</label>
    <br>
    <input type="text" id="channelName" name="channelName">
    <br>
    <input type="hidden" id="color" name="color">
    <button type="submit" id="submit">Submit</button>
</div>

<div class="hide" id="error">
</div>

<div class="form hide" id="confirmationForm">
    <p>Is this correct?<p>
    <div id="confirmationContent">   
    </div>
    <input type="hidden" id="channelId">
    <button id="yes" name="channelConfirmation">Yes</button>
    <button id="no" name="channelConfirmation">No</button>
</div>

<div id="addChannel">

</div>

<script>

    //Global Variables

    const form = {
        channelName: document.getElementById('channelName'),
        platform: document.getElementById('platform'),
        submit: document.getElementById('submit'),
        error: document.getElementById('error'),
        confirmationForm: document.getElementById('confirmationForm'),
        confirmationContent: document.getElementById('confirmationContent')
    };

    const confirmation = {
        confirmationForm: document.getElementById('confirmationForm'),
        confirmChannelId: document.getElementById('channelId'),
        confirmYes: document.getElementById('yes'),
        confirmNo: document.getElementById('no')
    };

    const newChannel = {
        addChannel: document.getElementById('addChannel')
    };

    const request = new XMLHttpRequest();

    let channelsAdded = 0;
    let noCounter = 0;
    let channelPlatform = null;
    let channelName = null;
    let channelImage = null;

    //Initial Search

    form.submit.addEventListener('click', () => {

        const img = document.createElement('img');
        const newSpan = document.createElement('span');
        const newDiv = document.createElement('div');

        channelPlatform = document.querySelector('input[name="platform"]:checked');

        if (!channelPlatform || !form.channelName.value) {
            newSpan.textContent = 'Missing search fields';
            form.error.appendChild(newSpan);
            form.error.classList.remove('hide');

            if (!channelPlatform) {
                form.platform.style.border = '1px solid red';
            }

            if (!form.channelName.value) {
                form.channelName.style.border = '1px solid red';
            }

            setTimeout(function () {
                form.error.classList.add('hide');
                form.platform.style.border = 'none';
                form.channelName.style.border = 'none';
                while (form.error.firstChild) {
                    form.error.removeChild(form.error.firstChild);
                }
            }, 10000);
        }

        request.onload = () => {
            responseObject = null;

            try {
                responseObject = JSON.parse(request.responseText);
            } catch (e) {
                console.error('Could not pass JSON');
            }

            if (responseObject) {
                handleResponse(responseObject);
            }
        };

        requestData = `platform=${channelPlatform.value}&channelName=${form.channelName.value}`;

        request.open("POST", "creator-search.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.send(requestData);

        noCounter = 0;

    });

    //

    confirmation.confirmYes.addEventListener('click', () => {

        console.log('yes click');

        request.onload = () => {
            responseObject = null;

            try {
                responseObject = JSON.parse(request.responseText);
            } catch (e) {
                console.log(request)
                console.error('Could not pass JSON');
            }

            if (responseObject) {
                handleResponse(responseObject);
            }
        };

        requestData = `channelId=${confirmation.confirmChannelId.value}`;

        request.open("POST", "creator-search.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.send(requestData);

    });

    confirmation.confirmNo.addEventListener('click', () => {
        noCounter++;

        const img = document.createElement('img');
        const newSpan = document.createElement('span');
        const newDiv = document.createElement('div');

        if (responseObject.channelData['items'][noCounter]) {

            while (form.confirmationContent.firstChild) {
                form.confirmationContent.removeChild(form.confirmationContent.firstChild);
            }

            console.log('here');
            console.log(responseObject.channelData['items'][noCounter]['snippet']);

            channelImage = responseObject.channelData['items'][noCounter]['snippet']['thumbnails']['default']['url'];
            img.src = channelImage
            img.classList.add('channelImage');
            form.confirmationContent.appendChild(img);

            channelName = responseObject.channelData['items'][noCounter]['snippet']['channelTitle'];
            newSpan.textContent = channelName;
            newSpan.classList.add('channelName');
            form.confirmationContent.appendChild(newSpan);

            confirmation.confirmChannelId.value = responseObject.channelData['items'][noCounter]['snippet']['channelId'];


            console.log('confirm no');
        }
        else {
            while (form.confirmationContent.firstChild) {
                form.confirmationContent.removeChild(form.confirmationContent.firstChild);
            }

            form.confirmationForm.classList.add('hide');

            newSpan.textContent = 'Please try another search query';
            form.error.appendChild(newSpan);
            form.error.classList.remove('hide');

            setTimeout(function () {
                form.error.classList.add('hide');
                while (form.error.firstChild) {
                    form.error.removeChild(form.error.firstChild);
                }
            }, 10000);

        }

    });

    function handleResponse (responseObject) {

        const img = document.createElement('img');
        const newSpan = document.createElement('span');
        const newDiv = document.createElement('div');

        if (responseObject.channelName) {

            console.log('initial searh response');
            console.log(responseObject);

            channelName = responseObject.channelName;
            channelImage = responseObject.channelImage;

            while (form.confirmationContent.firstChild) {
                form.confirmationContent.removeChild(form.confirmationContent.firstChild);
            }

            img.src = responseObject.channelImage;
            img.classList.add('channelImage');
            form.confirmationContent.appendChild(img);

            newSpan.textContent = responseObject.channelName;
            newSpan.classList.add('channelName');
            form.confirmationContent.appendChild(newSpan);

            confirmation.confirmChannelId.value = responseObject.channelId;

            form.confirmationForm.classList.remove('hide');
        }
        else if (responseObject.videoEmbed) {

            channelsAdded++;

            form.confirmationForm.classList.add('hide');

            console.log(responseObject);

            newDiv.classList.add('channelRow');
            newDiv.innerHTML = `${responseObject.videoEmbed} <div class="channelDetails">
            <img class="channelImage" src="${channelImage}"> 
            <span class="channelName"><a href="https://www.youtube.com/channel/${responseObject.channelId}" target="_blank">${channelName}</a></span>
            <span class="videoTitle">${responseObject.videoTitle}</span><span class="videoLive ${responseObject.videoLive}">${responseObject.videoLive}</span>
            <span class="videoDesc">${responseObject.videoDesc}</span>
            </div>`;

            newChannel.addChannel.appendChild(newDiv);

        }
        else {
            console.log('Did not detect response');
            console.log(responseObject);

            form.confirmationForm.classList.add('hide');

            if (responseObject.channelData['error']) {
                newSpan.innerHTML = responseObject.channelData['error']['message'];
            }
            else if (responseObject.recentVideos['error']) {
                newSpan.innerHTML = responseObject.recentVideos['error']['message'];
            }
            else {
                newSpan.textContent = 'Could not find a channel matching your query';
            }
            form.error.appendChild(newSpan);
            form.error.classList.remove('hide');

            setTimeout(function () {
                form.error.classList.add('hide');
                while (form.error.firstChild) {
                    form.error.removeChild(form.error.firstChild);
                }
            }, 10000);

        }
    };
</script>



</body>
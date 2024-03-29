// variables
// gebaut nach https://www.script-tutorials.com/demos/257/index.html

var dropArea = document.getElementById('dropArea');
var canvas = document.querySelector('canvas');
var context = canvas.getContext('2d');
var count = document.getElementById('count');
var destinationUrl = document.getElementById('upload_foto_url');
var result = document.getElementById('result');
var list = [];
var totalSize = 0;
var totalProgress = 0;
// main initialization
(function(){
  // init handlers
  function initHandlers() {
      dropArea.addEventListener('drop', handleDrop, false);
      dropArea.addEventListener('dragover', handleDragOver, false);
  }
  // draw progress
  function drawProgress(progress) {
      context.clearRect(0, 0, canvas.width, canvas.height); // clear context
      context.beginPath();
      context.strokeStyle = '#4B9500';
      context.fillStyle = '#4B9500';
      context.fillRect(0, 0, progress * 500, 20);
      context.closePath();
      // draw progress (as text)
      context.font = '16px Verdana';
      context.fillStyle = '#000';
      context.fillText('Fortschritt: ' + Math.floor(progress*100) + '%', 50, 15);
  }
  // drag over
  function handleDragOver(event) {
      event.stopPropagation();
      event.preventDefault();
      dropArea.className = 'hover';
  }
  // drag drop
  function handleDrop(event) {
      event.stopPropagation();
      event.preventDefault();
      processFiles(event.dataTransfer.files);
  }
  // process bunch of files
  function processFiles(filelist) {
    if (!filelist || !filelist.length || list.length) return;
    totalSize = 0;
    totalProgress = 0;
    result.textContent = '';
    for (var i = 0; i < filelist.length && i < 50; i++) {
      list.push(filelist[i]);
      totalSize += filelist[i].size;
    }
    uploadNext();
  }
  // on complete - start next file
  function handleComplete(size) {
    totalProgress += size;
    drawProgress(totalProgress / totalSize);
    uploadNext();
  }
  // update progress
  function handleProgress(event) {
      var progress = totalProgress + event.loaded;
      drawProgress(progress / totalSize);
  }
  // upload file
  function uploadFile(file, status) {
    // prepare XMLHttpRequest
    var xhr = new XMLHttpRequest();
    xhr.open('POST', destinationUrl.value);
    xhr.onload = function() {
      result.innerHTML += this.responseText;
      handleComplete(file.size);
    };
    xhr.onerror = function() {
      result.textContent = this.responseText;
      handleComplete(file.size);
    };
    xhr.upload.onprogress = function(event) {
      handleProgress(event);
    }
    xhr.upload.onloadstart = function(event) {
    }
    // prepare FormData
    var formData = new FormData();
		if (upload_only_file_metadata == 1) {
			console.log('yes');
			var data = JSON.stringify({
				name: file.name,
				size: file.size,
				lastmodified: file.lastModified
			});
		}
		else {
			var data = file;
		}
    formData.append(fotoLayerId_ + ';datei;' + fotoTableName_ + ';;Dokument;1;varchar;1', data);
    xhr.send(formData);
  }
  // upload next file
  function uploadNext() {
    if (list.length) {
      count.textContent = list.length - 1;
      dropArea.className = 'uploading';
      var nextFile = list.shift();
      if (nextFile.size >= 5242880) { // 5MB
        result.innerHTML += '<div class="f">Zu große Datei (max Dateigröße von 5 Mb überschritten)</div>';
        handleComplete(nextFile.size);
      }
			else {
        uploadFile(nextFile, status);
      }
    }
		else {
      dropArea.className = '';
    }
  }
  initHandlers();
})();
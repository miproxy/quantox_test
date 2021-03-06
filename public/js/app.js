var route;

var siteNameSpace  = {
   route_song : {
      onload : function() {
         route = this;
         _id("form-add-song").addEventListener("submit", route.addSong, false);
         var edit_buttons = document.getElementsByClassName("edit-song");
         for (var i = 0; i < edit_buttons.length; i++) {
            edit_buttons[i].addEventListener('click', this.fillModalFields, false);
         }
         _id("edit-song").addEventListener("submit", route.updateSong, false);
         var delete_forms = document.getElementsByClassName("delete-song");
         for (var i = 0; i < delete_forms.length; i++) {
            delete_forms[i].addEventListener('submit', this.deleteSong, false);
         }

      },

      fillModalFields: function(e) {
         _id("edit-id").value = e.target.dataset.id;
         _id("edit-artist").value = e.target.dataset.artist;
         _id("edit-track").value = e.target.dataset.track;
         _id("edit-link").value = e.target.dataset.link;
      },

      addSong: function(e){
         if(_id("useAjax").checked){
            e.preventDefault();
            var artist = "artist=" + _id("artist").value;
            var track = "track=" + _id("track").value;
            var link = "link=" + _id("link").value;
            var token = "_token=" + document.querySelector("._token").value;
            var submit = "submit_add_song=" + _id("submit_add_song").value
            var data = [artist, track, link, token, submit];
            var formData = data.join("&");
            postRequest("song/add", formData, route.addRow);
         }
      },

      addRow: function(data) {
         var tr = document.createElement("tr");
         tr.id = "tr_" + data.id;
         tr.innerHTML = `<th scope="row">${data.id}</th>
                        <td id="edit_artist_${data.id}">${data.artist}</td>
                        <td id="edit_track_${data.id}">${data.track}</td>
                        <td><a id="edit_link_${data.id}" target="_blank" class="btn btn-link" href="${data.link}" role="button">LINK</a></td>
                        <td><a id="edit_song_${data.id}" class="btn btn-primary edit-song" href="song/edit/${data.id}" role="button" data-toggle="modal" data-target="#modal-edit-song" data-id="${data.id}" data-artist="${data.artist}" data-track="${data.track}" data-link="${data.link}">EDIT</a></td>
                        <td>
                           <form id="form-${data.id}" action="song/delete/${data.id}" method="POST" class="delete-song" data-id="${data.id}">
                              <input type="hidden" name="_token" value="${csrf}"/>
                              <input type="hidden" name="_method" value="DELETE">
                              <input name="submit_delete_song" type="submit" class="btn btn-danger" value="DELETE">
                           </form>
                        </td>`;
         _id("table-body").appendChild(tr);
         var tb = _id("table-body");
         var num = _id("table-body").rows.length;
         _id("total").innerText = num;
         _id("artist").value = "";
         _id("track").value = "";
         _id("link").value = "";
         _id("edit_song_" + data.id).addEventListener('click', route.fillModalFields);
         _id("form-"+data.id).addEventListener('submit', route.deleteSong);

      },

      updateSong: function(e){
         if(_id("useAjax").checked){
            e.preventDefault();
            var id = "edit_id=" + _id("edit-id").value;
            var artist = "edit_artist=" + _id("edit-artist").value;
            var track = "edit_track=" + _id("edit-track").value;
            var link = "edit_link=" + _id("edit-link").value;
            var token = "_token=" + document.querySelector("._token").value;
            var submit = "submit_edit_song=" + _id("submit_edit_song").value;
            var method = "_method=PUT";
            var data = [id, artist, track, link, token, submit, method];
            var formData = data.join("&");
            postRequest("song/update", formData, route.updateRow);
         }
      },

      updateRow: function(data) {
         var id = data.id;
         _id("edit_artist_" + id).innerText = data.artist;
         _id("edit_track_" + id).innerText = data.track;
         _id("edit_link_" + id).href = data.link;
         _id("edit_song_" + id).dataset.artist = data.artist;
         _id("edit_song_" + id).dataset.track = data.track;
         _id("edit_song_" + id).dataset.link = data.link;
      },

      deleteSong: function(e) {
         if(_id("useAjax").checked) {
            e.preventDefault();
            var id = e.target.dataset.id;
            var token = "_token=" + document.querySelector("._token").value;
            var submit = "submit_delete_song=DELETE";
            var method = "_method=DELETE";
            var data = [token, submit, method];
            var formData = data.join("&");
            postRequest("song/delete/" + id, formData, function(data) {
               _id("tr_" + data.id).remove();
               var total = _id("table-body").rows.length;
               _id("total").innerText = total;
            });
         }
      }
   }
};

var postRequest = function(url, formData, callback) {
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function(){
         if (xmlhttp.readyState == XMLHttpRequest.DONE && xmlhttp.status == 200){
            if(callback !== null) {
               callback(JSON.parse(xmlhttp.responseText));
            }
         }
      }
      xmlhttp.open("POST", url, true);
      xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      xmlhttp.send(formData);
};

var init = function() {
   var routing_elements = document.querySelectorAll('[data-route*="route_"]');
   routing_elements.forEach(function(element) {
      if(siteNameSpace.hasOwnProperty(element.dataset.route)) {
         siteNameSpace[ element.dataset.route ].onload();
      }
   });
};

var _id = function(id) {
   return document.getElementById(id);
};

document.addEventListener("DOMContentLoaded", init);

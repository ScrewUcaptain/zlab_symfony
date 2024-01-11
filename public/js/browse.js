let gSelectedTags = [];
let gSelectedPlaylists = [];

function selectTag(tagName = "all", tagId = null) {
  if (gSelectedTags.includes(tagName)) return;
  if (tagName == "all") {
    gSelectedTags = ["all"];
    fetch("/playlist/all")
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          gSelectedPlaylists = [
            {
              type: "all",
              playlists: data.playlists,
            },
          ];
          renderPlaylists();
        }
      });
  } else {
    if (gSelectedTags.includes("all")) {
      gSelectedTags = [];
      gSelectedPlaylists = [];
    }
    gSelectedTags.push(tagName);
    {
      fetch("/playlist/tag/" + tagId)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            gSelectedPlaylists.push({
              type: tagName,
              playlists: data.playlists,
            });
            renderPlaylists();
          }
        });
    }
  }
  displayTags();
}

function renderPlaylists() {
  const playlistsContainer = document.querySelector(".playlists-container");
  playlistsContainer.innerHTML = "";
  let html = "";
  gSelectedPlaylists.forEach((plType) => {
    plType.playlists.forEach((playlist) => {
      html += `<a href='/playlist/${playlist.id}'>
	  	<div class="playlist-item" >	
			<div>
				<div>${playlist.name}</div>
				<div style="display:flex;align-items:center;">
					<box-icon name='star' type='solid' color='#fbd902' style="margin-right: 0.25rem"></box-icon>
					 ${playlist.likes}
				</div>
			</div>
			<div style="padding:0.25rem;">
				<img class="playlist-image" src="/images/${playlist.cover}" />
			</div>
		</div>
	</a>`;
    });
  });
  playlistsContainer.innerHTML = html;
}

function displayTags() {
  let html = "";
  gSelectedTags.forEach((tagName) => {
    html += `<div class="tag-item">
		<div class="tag-name">${tagName}</div> <box-icon onclick="deleteTag('${tagName}')" color="#FFF" name='x'></box-icon>
	</div>`;
  });
  document.querySelector(".selected-tags").innerHTML = html;
}

function deleteTag(tagName) {
  gSelectedTags = gSelectedTags.filter((tag) => tag != tagName);
  gSelectedPlaylists = gSelectedPlaylists.filter(
    (plType) => plType.type != tagName
  );
  displayTags();
  renderPlaylists();
}

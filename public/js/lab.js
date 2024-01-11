function openModal() {
  document.querySelector(".modal-playlist").style.display = "flex";
}
function closeModal() {
  document.querySelector(".modal-playlist").style.display = "none";
}

function createPlaylist() {
  const name = document.querySelector("#playlist-name").value;
  if (!name) {
    document.querySelector("#error").innerHTML = "Please enter a valid name";
    return;
  }
  const selectedTags = document.querySelector("#playlist-tags").selectedOptions;
  if (selectedTags.length === 0) {
    document.querySelector("#error").innerHTML =
      "Please select at least one tag";
    return;
  }
  const tags = [];
  for (let i = 0; i < selectedTags.length; i++) {
    tags.push(selectedTags[i].value);
  }
  const privacy = document.getElementById("private").checked;

  fetch("/playlist/new", {
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    method: "POST",
    body: JSON.stringify({
      name: name,
      tags: tags,
      privacy: privacy,
    }),
  }).then((response) => {
    console.log(response);
    if (response.ok) {
      location.href = "/lab";
    } else {
      document.querySelector("#error").innerHTML =
        "An error occured during process";
    }
  });
}

function deletePlaylist(id) {
  if (!confirm("Are you sure you want to delete this playlist?")) {
    return;
  }
  fetch("/playlist/" + id, {
    method: "DELETE",
  }).then((response) => {
    if (response.ok) {
      location.href = "/lab";
    } else {
      document.querySelector("#error").innerHTML =
        "An error occured during process";
    }
  });
}

function closeProfil() {
  document.querySelector(".profil").style.display = "none";
}
function openProfil() {
  document.querySelector(".profil").style.display = "block";
}

const modal = document.querySelector(".modal-song");
const inputSong = document.querySelector("#song-name");
const inputArtist = document.querySelector("#song-artist");
const inputYear = document.querySelector("#song-year");
const inputUrl = document.querySelector("#song-url");

function openModalSong() {
  modal.style.display = "flex";
}
function closeModalSong() {
  modal.style.display = "none";
}

function addSong() {
  const songName = inputSong.value;
  const songArtist = inputArtist.value;
  const songYear = inputYear.value;
  const songUrl = inputUrl.value;
  fetch("/playlist/" + playlistId + "/add", {
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    method: "POST",
    body: JSON.stringify({
      song: songName,
      artist: songArtist,
      year: songYear,
      url: songUrl,
    }),
  });

  closeModalSong();
}

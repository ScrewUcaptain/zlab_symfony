function deleteSong(id){
    if(confirm("Are you sure you want to delete this song ?")){
        fetch(gPaths.deleteSong + '?songId=' + id)
        .then(response => response.json())
        .then(data => {
            if(data.success){
                alert("Song deleted successfully")
                location.reload();
            }else{
                alert("An error occured")
            }
        })
    }
}

function deleteUser(id){
    if(confirm("Are you sure you want to delete this user ?")){
        fetch(gPaths.deleteUser + '?songUser=' + id)
        .then(response => response.json())
        .then(data => {
            if(data.success){
                alert("User deleted successfully")
                location.reload();
            }else{
                alert("An error occured")
            }
        })
    }
}

function deletePlaylist(id){
    if(confirm("Are you sure you want to delete this playlist ?")){
        fetch(gPaths.deletePlaylist + '?playlistId=' + id)
        .then(response => response.json())
        .then(data => {
            if(data.success){
                alert("Playlist deleted successfully")
                location.reload();
            }else{
                alert("An error occured")
            }
        })
    }
}

function deleteTag(id){
    if(confirm("Are you sure you want to delete this tag ?")){
        fetch(gPaths.deleteTag + '?tagId=' + id)
        .then(response => response.json())
        .then(data => {
            if(data.success){
                alert("Tag deleted successfully")
                location.reload();
            }else{
                alert("An error occured")
            }
        })
    }
}
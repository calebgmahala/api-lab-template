$(document).ready(function() {
  $("#sportsForm").submit(function(event) {
    var form = $(this);
    console.log($form)
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: "http://localhost:8080/api/sports/",
      data: form.serialize(), // serializes the form's elements.
      success: function(data) {
        window.location.replace("http://localhost:8080/interface");
      }
    });
  });
  $("#sportsEditForm").submit(function(event) {
    var iteam = $("#id").attr("value");
    var form = $(this);
    event.preventDefault();
    $.ajax({
      type: "PUT",
      url: "http://localhost:8080/api/sports/" + iteam,
      data: form.serialize(), // serializes the form's elements.
      success: function(data) {
        window.location.replace("http://localhost:8080/interface");
      }
    });
  });
  $( ".deletebtn" ).click(function() {
    var iteam = $(this).attr("data-id");
    if (confirm('Are you sure you want to delete this iteam?')) {
      $.ajax({
        type: "DELETE",
        url: "http://localhost:8080/api/sports/" + iteam,
        success: function(data) {
          alert("Success");
        }
      });
    }
  });
});

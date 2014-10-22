<footer>
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <p class="text-muted">
              © 2014 Hack4DK
            </p>
          </div>
        </div>
      </div>
    </footer>

    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(function(){

        $('.dropdown-menu li a').click(function(){
          $('li.dropdown a span.institution').text($(this).text());
        });

        $(".comment").on("click", function(a){
          $(this).toggleClass("bad");
        });     
        

      });
    </script>
  </body>
</html>
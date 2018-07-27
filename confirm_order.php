<?php
    $page_tittle = "User Authentication - Confirm Order Page";
    require "partials/headers.php";
?>
<div class="container center z-depth-3">

<div class="flag">
    <h1 class="grey lighten-2 teal-text text-darken-3">Welcome To Admin Page</h1>
    
    <?php 
        //TODO: remove if statement and just display logged in username
        if(isset($_SESSION['username'])){
            echo '<p class="lead">You are logged in as ' .$_SESSION['username'] .'<a href="logout.php"> Logout</a></p>';
        }else{
            echo '<script type="text/javascript">window.location.href = "login.php"</script>';
        }
    ?>
</div>
</div><!-- /.container -->
<div class="container">
    <section class="col col-lg-7">
        <form action="#">
            <script src="https://js.paystack.co/v1/inline.js"></script>
            <div class="row">
                <div class="input-field col s6">
                    <button type="button" class="btn btn-primary orange darken-2 right" name="pay_now" id="pay-now" tittle="Pay Now" onClick="saveOrderThenPayWithPaystack()">Pay Now</button>
                </div>
            </div>
        </form>
    </section>
</div>
<script>
    var orderObj = {
        useremail: "<? echo $_GET['email'];?>",
        userid: "<? echo $_GET['usrid'];?>",
        username: "<? echo $_GET['usr'];?>",
        paycategory: "<? echo $_GET['payref'];?>",
        amount: "1000000",
        orderid: "ordweb000001",
        cartid: "kd12345"
        //other params you want to save
    };
    function saveOrderThenPayWithPaystack(){
        //window.alert('trying to pay');
        //send the data to save to database using post
        window.alert('making payment');
        var posting = $.post('/saveorder.php', orderObj);

        posting.done(function(data){
            //check result from the attempt
            payWithPayStack(data);
        });
        posting.fail(function(data){
            window.alert('Failed to save data');
            payWithPayStack(data);
            //and if it failed to save do this
        });
    };
    function payWithPayStack(data){
        window.alert('making payment');
        var handler = PaystackPop.setup({            
            //This assumes you already created a constant named
            //PAYSTACK_PUBLIC_KEY with your public key from the
            //Paystack dashboard. You can as well just paste it
            //instead of creating the constant
            key: 'pk_test_6a23d42a2ac9cd58a44a1d32c3e9a255d71b6418',
            email: orderObj.email,
            amount: orderObj.amount,
            metadata: {
                cartid: orderObj.cartid,
                orderid: '',
                custom_fields: [
                    {
                        display_name: "Paid on",
                        Variable_name: "paid_on",
                    },
                    {
                        display_name: "Paid via",
                        variable_name: "paid_via",
                        value: "Online Payment"
                    }
                ]
            },
            callback: function(){
                //post to server to verify transaction before giving value
                var verifying = $.get('/verify.php?reference=' + response.reference);
                verifying.done(function(data){
                    //give value saved in data
                    verifyObj = json_decode(data);
                    if(verifyObj.verified == true){
                        alert("Your payment was successfull");
                    }
                });
            },
            onClose: function(){
                alert('Click "Pay Now" to retry payment');
            }
        });
        handler.openIframe();
    }
</script>
<?php include_once "partials/footers.php"; ?>
</body>
</html>
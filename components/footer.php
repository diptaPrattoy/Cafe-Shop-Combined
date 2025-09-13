<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="../css/style.css"> -->

    <style>
    .footer {
        margin-left: 0px;
        width: 100%;
        background-color: #9b9292ff;
        color: black;
        height: 400px;
    }

    .footer .grid {
        /* display: grid;
        grid-template-columns: repeat(auto-fit, minmax(27rem, 1fr));
        gap: 1.5rem;
        display: flex; */

    }

    .footer .grid .box {
        flex-direction: row;
        padding: 1rem;
        font-size: medium;
    }

    .footer .box {
        flex-direction: row;

    }

    .footer .grid .box {
        text-align: center;
        width: 100%;
        color: black;
        /* border-right: 1px solid black; */
    }

    .footer .grid .box img {
        height: 10rem;
        width: 100%;
        object-fit: contain;
        margin-bottom: 0.5rem;

    }

    .footer .grid .box h3 {
        margin: 1rem 0;
        font-size: 2rem;
        color: var(--black);
        text-transform: capitalize;

    }



    .footer .grid .box .phone {
        padding-bottom: 40px;
    }

    .footer .grid .box .phone1 {
        /* padding-top: 20px;  */
        border-top: 2px solid transparent;
    }

    /*  */



    .footer .grid .box a {
        display: block;
        flex-direction: row;
        font-size: 2rem;
        color: black;
        line-height: 1;
        padding: 15px;
        transition: 0.2s ease-in;
    }

    .footer .grid .box a:hover {
        /* letter-spacing: 0.3rem; */
        color: yellow;
        font-weight: 700;
    }

    .footer .grid .box .dateinfo {
        width: 100%;
        display: flex;
        align-items: center;
        border-left: 2px solid #000000ff;
        border-right: 2px solid #050505ff;

    }



    .footer .grid .box .dateinfo p {
        width: 50%;
        display: inline;
        float: left;
        font-size: 1.5rem;
        padding: 10px;
    }

    .footer .grid .box .dateinfo samp {
        width: 50%;
        float: right;
        font-size: 1.3rem;
    }

    hr {
        color: black;
    }
    </style>
</head>

<body>
    <footer class="footer">

        <section class="grid">

            <div class="box">

                <h3>quick links</h3>

                <a href="home.php"> Home </a>
                <a href="menu.php"> Menu </a>
                <a href="orders.php"> Order </a>
                <a href="about.php"> About </a>
            </div>


            <div class="box">

                <h3>Opening Hours</h3>
                <div class="dateinfo">
                    <p>MONDAY </p> <samp>CLOSED</samp>
                </div>
                <div class="dateinfo">
                    <p>TUESDAY </p> <samp>9.00 - 22.00</samp>
                </div>
                <div class="dateinfo">
                    <p>WEDNESDAY </p><samp>9.00 - 22.00</samp>
                </div>
                <div class="dateinfo">
                    <p>THURSDAY </p><samp>9.00 - 22.00</samp>
                </div>
                <div class="dateinfo">
                    <p>FRIDAY </p> <samp>9.00 - 22.00</samp>
                </div>
                <div class="dateinfo">
                    <p>SATURDAY </p> <samp>9.00 - 22.00</samp>
                </div>
                <div class="dateinfo">
                    <p>SUNDAY </p><samp>*10.00 - 20.00</samp>
                </div>


            </div>

            <div class="box">
                <h3>contact info</h3>
                <div class="phone">
                    <p> <i class="fas fa-phone"></i> +880168908900</p>
                    <p> <i class="fas fa-phone"></i> +8801732183023 </a>
                    <p> <i class="fas fa-envelope"></i>CozyCafe@gmail.com </p>
                </div>

                <div class="phone1">
                    <h3>Branch Location</h3>

                    <p> <i class="fas fa-map-marker-alt"></i> 408/1 (Old KA 66/1),
                        Kuratoli, Khilkhet,Dhaka 1229, Bangladesh
                    </p>
                    <p> <i class="fas fa-envelope"></i> CozyCafe.shop@gmail.com </p>
                </div>
            </div>




        </section>


    </footer>

</body>

</html>
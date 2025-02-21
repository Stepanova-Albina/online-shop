<div class="container signin">
    <p><a href="/profile">МОЙ ПРОФИЛЬ</a></p>
</div>
<div class="container signin">
    <p><a href="/catalog">КАТАЛОГ</a></p>
</div>
<div class="container">
    <h3>Корзина</h3>
    <div class="card-deck">
        <?php if (isset($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="card text-center">
                <a href="#">
                    <img class="card-img-top" src="<?php echo $product['image_url'];?>" alt="Card image">
                    <div class="card-body">
                        <p class="card-text text-muted"><?php echo $product['name'];?></p>
                        <a href="#"><h5 class="card-title"><?php echo $product['description'];?></h5></a>
                        <div class="card-footer">
                            Стоимость: <?php echo $product['price'];?>
                        </div>
                    </div>
                </a>
            </div>

            <form action="/add-product" method="POST">
                <div class="container">

                    <input type="hidden" placeholder="Enter Product-id" name="product_id" value="<?php echo $product['id'];?>" id="product_id" required>

                    <label for="amount"><b>Количество: <?php echo $product['amount'];?></b></label>

                    <hr>

                </div>

                <div class="container signin">
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</div>
<?php endif;?>
<style>
    a {
        color: #04AA6D;
    }
    .signin {
        background-color: #f1f1f1;
        text-align: left;
    }
    body {
        font-style: sans-serif;
    }

    a {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }

    h3 {
        line-height: 3em;
    }
    .card-img-top {
        width: 250px;
        height: 350px;
        object-fit: cover;
    }

    .card {
        max-width: 16rem;
    }

    .card:hover {
        box-shadow: 1px 2px 10px lightgray;
        transition: 0.2s;
    }

    .card-header {
        font-size: 13px;
        color: gray;
        background-color: white;
    }

    .text-muted {
        font-size: 11px;
    }

    .card-footer{
        font-weight: bold;
        font-size: 18px;
        background-color: white;
    }
    .registerbtn {
        background-color: #04AA6D;
        color: white;
    }

</style>

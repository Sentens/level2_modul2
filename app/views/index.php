<?php $this->layout('template', ['title' => 'Главная']) ?>
        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><h3>Комментарии</h3></div>
                <?= flash()->display();?>
                            <div class="card-body">
                    <?php foreach ($comments as $value) {?>
                                <div class="media">
                                  <img src="img/<?=$this->e($value['user_photo'])?>" class="mr-3" alt="..." width="64" height="64">
                                  <div class="media-body">
                                    <h5 class="mt-0"><?=$this->e($value['username'])?></h5> 
                                    <span><small><?=$this->e(date('d/m/Y', strtotime($value['date'])))?></small></span>
                                    <p>
                                        <?=$this->e($value['comment'])?>
                                    </p>
                                  </div>
                                </div>

                    <?php };?>
                            </div>
<nav aria-label="Page navigation example">
 <?php echo $paginator; ?>
</nav>

        
                        </div>
                    </div>



<?php if ($_SESSION['auth_logged_in']): ?>
                    <div class="col-md-12" style="margin-top: 20px;">
                        <div class="card">
                            <div class="card-header"><h3>Оставить комментарий</h3></div>
                            <div class="card-body">
                                <form action="/index/addComment" method="post">
                                    <div class="form-group">
                                  <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Сообщение</label>
                                    <textarea name="comment" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                  </div>
                                  <button type="submit" class="btn btn-success">Отправить</button>
                                </form>
                            </div>
                        </div>
                    </div>

<?php else: ?>

<div style="width: 100%; padding: 10px; margin: 15px; display: flex; justify-content: flex-start; background: #e1f0fc;">
                            Чтобы оставить комментарий &nbsp;<a href="/login"> авторизируйтесь</a></div>

<?php endif ?>
                </div>
            </div>
        </main>

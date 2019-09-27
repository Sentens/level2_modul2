<?php $this->layout('template', ['title' => 'Админка']) ?>
<main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><h3>Админ панель</h3></div>

                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Аватар</th>
                                            <th>Имя</th>
                                            <th>Дата</th>
                                            <th>Комментарий</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                    <?php 


                    foreach ($comments as $value) { ?>

                                        <tr>
                                            <td>
                                                <img src="img/<?=$this->e($value['user_photo'])?>" alt="" class="img-fluid" width="64" height="64">
                                            </td>
                                            <td><?=$this->e($value['username'])?></td>
                                            <td><?=$this->e(date('d/m/Y', strtotime($value['date'])))?></td>
                                            <td><?=$this->e($value['comment'])?></td>
                                            <td class="w-25">
                                                <?php if ($this->e($value['hidden']) == '1'): ?>
                                                    <a href="/admin/show_comment/<?=$this->e($value['id'])?>" class="btn btn-success">Разрешить</a>
                                                
                                                    <?php elseif ($this->e($value['hidden']) == '0'): ?>
                                                    <a href="/admin/hide_comment/<?=$this->e($value['id'])?>" class="btn btn-warning">Запретить</a>
                                                <?php endif ?>
                                                <a href="/admin/delete_comment/<?=$this->e($value['id'])?>" onclick="return confirm('are you sure?')" class="btn btn-danger">Удалить</a>
                                            </td>
                                        </tr>

  <?php }; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

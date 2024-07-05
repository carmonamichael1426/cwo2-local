<!-- Modal -->
<div class="modal fade" id="managersKey" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header bg-dark rounded-0">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-key"></i> Managers Key</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" enctype="multipart/form-data" ng-submit="authorizeKey($event)">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text rounded-0" id="inputGroup-sizing-default"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control rounded-0" name="username" autocomplete="off" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text rounded-0" id="inputGroup-sizing-default"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" class="form-control rounded-0" name="password" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn bg-gradient-primary btn-flat"><i class="fas fa-key"></i> Authorize</button>
                    <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
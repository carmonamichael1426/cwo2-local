<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="supplier-controller">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row" ng-init="loadSuppliersData();">
                <div class="col-lg-12">
                    <div class="card card-outline card-style-1">
                        <div class="card-header bg-dark rounded-0">
                            <div class="content-header" style="padding: 0px">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel-body">
                                            <strong>LIST OF SUPPLIERS IMPLEMENTED BY BATCH, ALONG WITH THEIR CORRESPONDING PRICES, TO BE USED IN CHECKING OR CREATING A PROFORMA INVOICE, AS WELL AS THE TYPE OF PROFORMA ENTRY.</strong><br>
                                            <!-- <p>List of the suppliers, along with their corresponding prices, to be used in checking or creating a proforma invoice, as well as the type of proforma entry.</p> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <tbody>                                   
                                    <tr data-widget="expandable-table" aria-expanded="false" class="bg-primary">
                                        <td>
                                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            FIRST BATCH Date of Implementation : June 14, 2022
                                        </td>
                                    </tr>
                                    <tr class="expandable-body">
                                        <td>
                                            <div class="p-0">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <th class="text-center" style="width:50%">SUPPLIER NAME</th>
                                                        <th class="text-center" style="width:30%">PRICE USED</th>
                                                        <th class="text-center" style="width:20%">PRO-FORMA ENTRY</th>
                                                    </thead>
                                                    <tbody>     
                                                        <tr ng-repeat="s in supplierData | orderBy:'supplier_name'" ng-if="s.batch == '1st' && s.status =='1'" ng-cloak>
                                                            <td class="text-center">{{s.supplier_name}}</td>
                                                            <td class="text-center">{{s.price_used}}</td>
                                                            <td class="text-center">{{s.proforma}}</td>
                                                        </tr> 
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr data-widget="expandable-table" aria-expanded="false" class="bg-success">
                                        <td>
                                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            SECOND BATCH Date of Implementation : October 28, 2022
                                        </td>
                                    </tr>
                                    <tr class="expandable-body">
                                        <td>
                                            <div class="p-0">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <th class="text-center" style="width:50%">SUPPLIER NAME</th>
                                                        <th class="text-center" style="width:30%">PRICE USED</th>
                                                        <th class="text-center" style="width:20%">PRO-FORMA ENTRY</th>
                                                    </thead>
                                                    <tbody>     
                                                        <tr ng-repeat="s in supplierData | orderBy:'supplier_name'" ng-if="s.batch == '2nd' && s.status =='1'" ng-cloak>
                                                            <td class="text-center">{{s.supplier_name}}</td>
                                                            <td class="text-center">{{s.price_used}}</td>
                                                            <td class="text-center">{{s.proforma}}</td>
                                                        </tr> 
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr data-widget="expandable-table" aria-expanded="false" class="bg-warning">
                                        <td>
                                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            THIRD BATCH Date of Implementation : December 01, 2022
                                        </td>
                                    </tr>
                                    <tr class="expandable-body">
                                        <td>
                                            <div class="p-0">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <th class="text-center" style="width:50%">SUPPLIER NAME</th>
                                                        <th class="text-center" style="width:30%">PRICE USED</th>
                                                        <th class="text-center" style="width:20%">PRO-FORMA ENTRY</th>
                                                    </thead>
                                                    <tbody>     
                                                        <tr ng-repeat="s in supplierData | orderBy:'supplier_name'" ng-if="s.batch == '3rd' && s.status =='1'" ng-cloak>
                                                            <td class="text-center">{{s.supplier_name}}</td>
                                                            <td class="text-center">{{s.price_used}}</td>
                                                            <td class="text-center">{{s.proforma}}</td>
                                                        </tr> 
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr data-widget="expandable-table" aria-expanded="false" class="bg-danger">
                                        <td>
                                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            FOURTH BATCH Date of Implementation : February 06, 2023
                                        </td>
                                    </tr>
                                    <tr class="expandable-body">
                                        <td>
                                            <div class="p-0">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <th class="text-center" style="width:50%">SUPPLIER NAME</th>
                                                        <th class="text-center" style="width:30%">PRICE USED</th>
                                                        <th class="text-center" style="width:20%">PRO-FORMA ENTRY</th>
                                                    </thead>
                                                    <tbody>     
                                                        <tr ng-repeat="s in supplierData | orderBy:'supplier_name'" ng-if="s.batch == '4th' && s.status =='1'" ng-cloak>
                                                            <td class="text-center">{{s.supplier_name}}</td>
                                                            <td class="text-center">{{s.price_used}}</td>
                                                            <td class="text-center">{{s.proforma}}</td>
                                                        </tr> 
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr data-widget="expandable-table" aria-expanded="false" class="bg-info">
                                        <td>
                                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            FIFTH BATCH Date of Implementation : February 24, 2023
                                        </td>
                                    </tr>
                                    <tr class="expandable-body">
                                        <td>
                                            <div class="p-0">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <th class="text-center" style="width:50%">SUPPLIER NAME</th>
                                                        <th class="text-center" style="width:30%">PRICE USED</th>
                                                        <th class="text-center" style="width:20%">PRO-FORMA ENTRY</th>
                                                    </thead>
                                                    <tbody>     
                                                        <tr ng-repeat="s in supplierData | orderBy:'supplier_name'" ng-if="s.batch == '5th' && s.status =='1'" ng-cloak>
                                                            <td class="text-center">{{s.supplier_name}}</td>
                                                            <td class="text-center">{{s.price_used}}</td>
                                                            <td class="text-center">{{s.proforma}}</td>
                                                        </tr> 
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr data-widget="expandable-table" aria-expanded="false" class="bg-primary">
                                        <td>
                                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            SIXTH BATCH Date of Implementation : April 03, 2023
                                        </td>
                                    </tr>
                                    <tr class="expandable-body">
                                        <td>
                                            <div class="p-0">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <th class="text-center" style="width:50%">SUPPLIER NAME</th>
                                                        <th class="text-center" style="width:30%">PRICE USED</th>
                                                        <th class="text-center" style="width:20%">PRO-FORMA ENTRY</th>
                                                    </thead>
                                                    <tbody>     
                                                        <tr ng-repeat="s in supplierData | orderBy:'supplier_name'" ng-if="s.batch == '6th' && s.status =='1'" ng-cloak>
                                                            <td class="text-center">{{s.supplier_name}}</td>
                                                            <td class="text-center">{{s.price_used}}</td>
                                                            <td class="text-center">{{s.proforma}}</td>
                                                        </tr> 
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr data-widget="expandable-table" aria-expanded="false" class="bg-success">
                                        <td>
                                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            SEVENTH BATCH Date of Implementation : May 19, 2023
                                        </td>
                                    </tr>
                                    <tr class="expandable-body">
                                        <td>
                                            <div class="p-0">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <th class="text-center" style="width:50%">SUPPLIER NAME</th>
                                                        <th class="text-center" style="width:30%">PRICE USED</th>
                                                        <th class="text-center" style="width:20%">PRO-FORMA ENTRY</th>
                                                    </thead>
                                                    <tbody>     
                                                        <tr ng-repeat="s in supplierData | orderBy:'supplier_name'" ng-if="s.batch == '7th' && s.status =='1'" ng-cloak>
                                                            <td class="text-center">{{s.supplier_name}}</td>
                                                            <td class="text-center">{{s.price_used}}</td>
                                                            <td class="text-center">{{s.proforma}}</td>
                                                        </tr> 
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
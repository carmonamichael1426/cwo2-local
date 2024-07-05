window.myApp.controller('supplierledger-controller', ($scope, $http) => {
    $scope.getSuppliers = () => {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliers'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }

    $scope.getDetails = () => {
        $http({
            method: 'POST',
            url: $base_url + 'getSuppliers'
        }).then(function successCallback(response) {
            angular.forEach(response.data, function(item) {
                if ($scope.supplierName == item.supplier_id) {
                    $scope.supplierCode = item.supplier_code;
                    $scope.supplierAcroname = item.acroname;
                }
            });
        });
    }

    $scope.generateLedger = (supplier_id) => {
        $http({
            method: 'POST',
            url: '../reportsController/ledgerController/getLedger/' + supplier_id
        }).then(function successCallback(response) {

            if (response.data != '') {
                $scope.ledger = response.data;
                $scope.tableShow = true;
                $(document).ready(function() {
                    $('#supplierLedgerTable').DataTable({
                        dom: `<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'pB>>`,
                        "lengthMenu": [10, 25, 50, 75, 100],
                        buttons: [
                            'excel'
                        ]
                    });
                });
            } else {
                $scope.tableShow = false;
                Swal.fire({
                    title: 'No Data Found.'
                })
            }
        });
    }

    $scope.details = (data) => {
        if (data.doc_type == 'Invoice') {
            $scope.invoiceType = true;
            $scope.paymentType = false;
            $scope.invoiceData(data);
        } else if (data.doc_type == 'Payment') {
            $scope.paymentType = true;
            $scope.invoiceType = false;
            $scope.paymentData(data);
        } else if (data.doc_type == 'SOP') {

        }
    }

    $scope.paymentData = (data) => {
        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'getDataLedger',
            data: $.param(data),
            responseType: 'json'
        }).then(function successCallback(response) {
            $scope.crfNo = response.data.crf.crf_no;
            $scope.crfDate = response.data.crf.crf_date;
            $scope.collectorsName = response.data.crf.collector_name;
            $scope.crfAmount = response.data.crf.crf_amt;
            $scope.remarks = response.data.crf.remarks;
            $scope.paidAmount = response.data.crf.paid_amt;

            // FOR PROFORMA DATA
            $scope.proformaHeader = response.data.header;
            $scope.proformaLines = response.data.lines;
            $scope.linesTotal = response.data.total;
            $scope.totalDiscount = response.data.totalDiscount;
            $scope.totalVAT = response.data.totalVAT;

            // FOR PURCHASE INVOICE
            $scope.piheader = response.data.piHeader;
            $scope.PILines = response.data.piLines;
            $scope.PITotalAmount = response.data.totalPI;
        });
    }

    $scope.invoiceData = (data) => {
        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'getDataLedgerInvoice',
            data: $.param(data),
            responseType: 'json'
        }).then(function successCallback(response) {

            $scope.pi_no = response.data.PI_header.pi_no;;
            $scope.vendor_invoice_no = response.data.PI_header.vendor_invoice_no;
            $scope.posting_date = response.data.PI_header.posting_date;
            $scope.amt_including_vat = response.data.PI_header.amt_including_vat;

            $scope.PI_Lines = response.data.PI_line;
            $scope.PI_TotalAmount = response.data.PI_Total;
        });
    }

    $scope.checkDetails = function(data) {
        console.log(data);
    }
});
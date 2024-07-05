<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ============ DEFAULT CONTROLLERS ============ //
$route['default_controller'] = 'baseController/login';
$route['home'] = 'baseController/home';
$route['login'] = 'baseController/checkCredentials';
$route['logout'] = 'baseController/endSession';
$route['about'] = 'baseController/about';
$route['contactus'] = 'baseController/contactus';
$route['messageus'] = 'baseController/messageus';
$route['emailus'] = 'baseController/emailus';
// ============ DEFAULT CONTROLLERS ============ //

// ============ TESTING CONTROLLERS ============ //
$route['testing'] = 'baseController/testing';
$route['testPDF'] = 'Testingcontroller/testPDF';
$route['cwoSlip'] = 'Testingcontroller/cwoSlip';
$route['authorize'] = 'baseController/authorize';
$route['getSOP'] = 'Testingcontroller/getSOP';
$route['printProfVSCRF'] = 'Testingcontroller/printProfVSCRF';
$route['printSOP'] = 'Testingcontroller/printSOP';
$route['soscd'] = 'Testingcontroller/seniorDiscount';
$route['deductionReport'] = 'Testingcontroller/deductionReport';
$route['poclosing'] = 'Testingcontroller/poclosing';
$route['testmatching2'] = 'Testingcontroller/testmatching2';
$route['changepasstest'] = 'Testingcontroller/changepasstest';

// ============ TESTING CONTROLLERS ============ //

// ============ MASTER FILES ============ //
$route['masterfiles/(:any)'] = 'baseController/masterfile/$1';
$route['addCustomer'] = 'masterfileController/customercontroller/addCustomer';
$route['updateCustomer'] = 'masterfileController/customercontroller/updateCustomer';
$route['deactivateCustomer'] = 'masterfileController/customercontroller/deactivateCustomer';
$route['fetchCustomers'] = 'masterfileController/customercontroller/fetchCustomers';

$route['fetchSuppliers'] = 'masterfileController/suppliercontroller/fetchSuppliers';
$route['addSupplier']['POST'] = 'masterfileController/suppliercontroller/addSupplier';
$route['uploadSupplier'] = 'masterfileController/suppliercontroller/uploadSupplier';
$route['updateSupplier']['POST'] = 'masterfileController/suppliercontroller/updateSupplier';
$route['deactivateSupplier'] = 'masterfileController/suppliercontroller/deactivateSupplier';
$route['updateSupSetting'] = 'masterfileController/suppliercontroller/updateSupSetting';
$route['pricelist'] = 'masterfileController/suppliercontroller/pricelist';
$route['vispage/(:any)'] = 'masterfileController/suppliercontroller/VIS/$1';

$route['generateItems'] = 'masterfileController/itemCodeController/generateItems';
$route['uploadItems'] = 'masterfileController/itemCodeController/uploadItems';
$route['updateItemCodes'] = 'masterfileController/itemCodeController/updateItemCodes';
$route['getNoMapItems'] = 'masterfileController/itemCodeController/getNoMapItems';
$route['saveMapItems'] = 'masterfileController/itemCodeController/saveMapItems';
$route['updateNewItems'] = 'masterfileController/itemCodeController/updateNewItems';
$route['deleteItem'] = 'masterfileController/itemCodeController/deleteItem';
$route['uploadMapping'] = 'masterfileController/itemCodeController/uploadMapping';

$route['getDeals'] = 'masterfileController/vendorsdealcontroller/getDeals';
$route['uploadDeals'] = 'masterfileController/vendorsdealcontroller/uploadDeals';
$route['updateDeals'] = 'masterfileController/vendorsdealcontroller/updateDeals';
$route['loadItemDeptCode'] = 'masterfileController/vendorsdealcontroller/loadItemDeptCode';
$route['submitManualSetup'] = 'masterfileController/vendorsdealcontroller/submitManualSetup';
$route['getDealsDetail'] = 'masterfileController/vendorsdealcontroller/getDealsDetail';
$route['addVDLine'] = 'masterfileController/vendorsdealcontroller/addVDLine';
$route['setDiscount'] = 'masterfileController/vendorsdealcontroller/setDiscount';
$route['getDiscountsUsed'] = 'masterfileController/vendorsdealcontroller/getDiscountsUsed';

$route['addType'] = 'masterfileController/deductioncontroller/addType';
$route['saveDeduction'] = 'masterfileController/deductioncontroller/saveDeduction';
$route['loadDeductions'] = 'masterfileController/deductioncontroller/loadDeductions';
$route['editDeduction'] = 'masterfileController/deductioncontroller/editDeduction';
$route['deactivateDeduction'] = 'masterfileController/deductioncontroller/deactivateDeduction';

$route['addChargesType'] = 'masterfileController/chargescontroller/addChargesType';
$route['editChargesType'] = 'masterfileController/chargescontroller/editChargesType';
$route['deactivateChargesType'] = 'masterfileController/chargescontroller/deactivateChargesType';

$route['loadVAT'] = 'masterfileController/vatcontroller/loadVAT';
$route['addVAT'] = 'masterfileController/vatcontroller/addVAT';
$route['updateVAT'] = 'masterfileController/vatcontroller/updateVAT';
$route['deactivateVAT'] = 'masterfileController/vatcontroller/deactivateVAT';
// ============ MASTER FILES ============ //

$route['getUsers'] = 'masterfileController/usercontroller/getUsers';
$route['addUser'] = 'masterfileController/usercontroller/addUser';
$route['updateUser'] = 'masterfileController/usercontroller/updateUser';
$route['deactivate'] = 'masterfileController/usercontroller/deactivate';
$route['getoldpass'] = 'masterfileController/usercontroller/getoldpass';
$route['changepassword'] = 'masterfileController/usercontroller/changepassword';
$route['resetpassword'] = 'masterfileController/usercontroller/resetpassword';
$route['searchEmp'] = 'masterfileController/usercontroller/searchEmployee';
$route['addEmp'] = 'masterfileController/usercontroller/addEmployee';
$route['getOldUsername'] = 'masterfileController/usercontroller/getOldUsername';
$route['changeUsername'] = 'masterfileController/usercontroller/changeUsername';
//=================**===ADDED VIS SAVING INFORMATION===**======================================//

$route['supplierName'] = 'masterfileController/VisController/supplierName';
$route['visInfo'] = 'masterfileController/VisController/visInfo';
$route['updateVisinfo'] = 'masterfileController/VisController/updateVisinfo';

$route['fetchSuppliersname'] = 'masterfileController/VisController/fetchSuppliersname';
$route['fetchVisID'] = 'masterfileController/VisController/fetchVisID';

$route['updateVIS'] = 'masterfileController/VisController/updateVIS';

$route['uploadVisDocu'] = 'masterfileController/VisController/uploadVisDocu';
//========================================================================================//
// ============ PO ============ /
$route['getSuppliersForPO'] = 'transactionControllers/pocontroller/getSuppliersForPO';
$route['getCustomersForPO'] = 'transactionControllers/pocontroller/getCustomersForPO';
$route['uploadPo'] = 'transactionControllers/pocontroller/uploadPo';
$route['getPOs'] = 'transactionControllers/pocontroller/getPOs';
$route['getPoDetails/(:any)/(:any)'] = 'transactionControllers/pocontroller/getPoDetails/$1/$2';
$route['loadItems'] = 'transactionControllers/pocontroller/loadItems';
$route['createPo'] = 'transactionControllers/pocontroller/createPo';

// ============ PO VS PROFORMA ============ //
$route['getSuppliers'] = 'transactionControllers/povsproformacontroller/getSuppliers';
$route['getCustomers'] = 'transactionControllers/povsproformacontroller/getCustomers';
$route['getPurchaseOrder/(:any)/(:any)'] = 'transactionControllers/povsproformacontroller/getPurchaseOrder/$1/$2';
$route['uploadProforma'] = 'transactionControllers/povsproformacontroller/uploadProforma';
$route['getPendingMatchesPRF'] = 'transactionControllers/povsproformacontroller/getPendingMatches';
$route['matchPOandProforma'] = 'transactionControllers/povsproformacontroller/matchPOandProforma';
$route['getProforma'] = 'transactionControllers/povsproformacontroller/getProforma';
$route['getProforma/(:any)/(:any)/(:any)'] = 'transactionControllers/povsproformacontroller/getProforma/$1/$2/$3';
$route['updateProformaLine'] = 'transactionControllers/povsproformacontroller/updateProformaLine';
$route['getHistory/(:any)'] = 'transactionControllers/povsproformacontroller/getHistory/($1)';
$route['replaceProforma'] = 'transactionControllers/povsproformacontroller/replaceProforma';
$route['addDiscount'] = 'transactionControllers/povsproformacontroller/addDiscount';
$route['getDiscount/(:any)'] = 'transactionControllers/povsproformacontroller/getDiscount/$1';
$route['getPos'] = 'transactionControllers/povsproformacontroller/getPos';
$route['getPisDetails'] = 'transactionControllers/povsproformacontroller/getPisDetails';
$route['additionals'] = 'transactionControllers/povsproformacontroller/additionals';
$route['priceCheck'] = 'transactionControllers/povsproformacontroller/priceCheck';
$route['getPricing'] = 'transactionControllers/povsproformacontroller/getPricing';
$route['getProfPriceCheckStats'] = 'transactionControllers/povsproformacontroller/getProfPriceCheckStatistics';

//CREATE PROFORMA
$route['searchPos'] = 'transactionControllers/povsproformacontroller/searchPos';
$route['PoDetails'] = 'transactionControllers/povsproformacontroller/PoDetails';
$route['saveProforma'] = 'transactionControllers/povsproformacontroller/saveProforma';
//CREATE PROFORMA

// NEW PROFORMA ROUTES ---- ONGOING
$route['getMatchItems'] = 'transactionControllers/povsproformacontroller/getMatchItems';


// ============ PROFORMA VS PI ============ //
$route['getSuppliersForPI'] = 'transactionControllers/proformavspicontroller/getSuppliersForPI';
$route['getCustomersForPI'] = 'transactionControllers/proformavspicontroller/getCustomersForPI';
$route['uploadPi'] = 'transactionControllers/proformavspicontroller/uploadPi';
$route['getPIs'] = 'transactionControllers/proformavspicontroller/getPIs';
$route['getPiDetails'] = 'transactionControllers/proformavspicontroller/getPiDetails';
$route['updatePrice'] = 'transactionControllers/proformavspicontroller/updatePrice';
$route['getItemPriceLog'] = 'transactionControllers/proformavspicontroller/getItemPriceLog';
$route['getCrfInPI'] = 'transactionControllers/proformavspicontroller/getCrfInPI';
$route['getProfPiInCrf'] = 'transactionControllers/proformavspicontroller/getProfPiInCrf';
$route['applyPiToCrf'] = 'transactionControllers/proformavspicontroller/applyPiToCrf';
$route['untagPiFromCrf'] = 'transactionControllers/proformavspicontroller/untagPiFromCrf';
$route['managersKey'] = 'transactionControllers/proformavspicontroller/managersKey';
$route['checkUserType'] = 'transactionControllers/proformavspicontroller/checkUserType';
$route['changeStatus'] = 'transactionControllers/proformavspicontroller/changeStatus';
$route['matchProformaVsPi'] = 'transactionControllers/proformavspicontroller/matchProformaVsPi';
$route['viewMatchedUnmatchedItems'] = 'transactionControllers/proformavspicontroller/viewMatchedUnmatchedItems';
$route['uploadCm'] = 'transactionControllers/proformavspicontroller/uploadCm';
$route['viewCMDetails'] = 'transactionControllers/proformavspicontroller/viewCMDetails';
$route['changeAuditStatus'] = 'transactionControllers/proformavspicontroller/changeAuditStatus';
$route['getPo'] = 'transactionControllers/proformavspicontroller/getPo';
$route['searchtub_po'] = 'transactionControllers/proformavspicontroller/searchPoforTubigon';
$route['uploadPiTub'] = 'transactionControllers/proformavspicontroller/uploadPiTubigon';
$route['getPiStats'] = 'transactionControllers/proformavspicontroller/piStatistics';

// ===== NEW TAGGING and MATCHING //
$route['loadCRFS'] = 'transactionControllers/proformavspicontroller/loadCRFS';
$route['searchProf/(:any)/(:any)'] = 'transactionControllers/proformavspicontroller/searchProf/$1/$2';
$route['applyProforma/(:any)'] = 'transactionControllers/proformavspicontroller/applyProforma/$1';
$route['untagProf'] = 'transactionControllers/proformavspicontroller/untagProf';
$route['ProfvsPi'] = 'transactionControllers/proformavspicontroller/ProfvsPi';
$route['searchCrf/(:any)/(:any)'] = 'transactionControllers/proformavspicontroller/searchCrf/$1/$2';
// ===== NEW TAGGING and MATCHING //

// ===== NEW MATCHING ROUTE FOR PSI VS PI ===== //
$route['matching2'] = 'transactionControllers/proformavspicontroller/matching2';
// ===== NEW MATCHING ROUTE FOR PSI VS PI ===== //


// ============ PROFORMA VS CRF ============ //
$route['getSuppliersForCRF'] = 'transactionControllers/proformavscrfcontroller/getSuppliersForCRF';
$route['getSop/(:any)'] = 'transactionControllers/proformavscrfcontroller/getSop/$1';
$route['getCustomersForCRF'] = 'transactionControllers/proformavscrfcontroller/getCustomersForCRF';
$route['getCrfs'] = 'transactionControllers/proformavscrfcontroller/getCrfs';
$route['uploadCrf'] = 'transactionControllers/proformavscrfcontroller/uploadCrf';
$route['matchProformaVsCrf'] = 'transactionControllers/proformavscrfcontroller/matchProformaVsCrf';
$route['getUnAppliedProforma/(:any)/(:any)'] = 'transactionControllers/proformavscrfcontroller/getUnAppliedProforma/$1/$2';
$route['getAppliedProforma/(:any)/(:any)'] = 'transactionControllers/proformavscrfcontroller/getAppliedProforma/$1/$2';
$route['applyProforma/(:any)/(:any)'] = 'transactionControllers/proformavscrfcontroller/applyProforma/$1/$2';
$route['untagProforma'] = 'transactionControllers/proformavscrfcontroller/untagProforma';
$route['auditCrf'] = 'transactionControllers/proformavscrfcontroller/auditCrf';
$route['tagAsMatchedCrf'] = 'transactionControllers/proformavscrfcontroller/tagAsMatchedCrf';
$route['replaceCRF'] = 'transactionControllers/proformavscrfcontroller/replaceCRF';
$route['tagAsClosedCRF'] = 'transactionControllers/proformavscrfcontroller/tagAsClosedCRF';
$route['trackCRF'] = 'transactionControllers/proformavscrfcontroller/trackCRF';
$route['getCrfStats'] = 'transactionControllers/proformavscrfcontroller/crfStatistics';


// ============ SOP ============ //
$route['getSuppliersSop'] = 'transactionControllers/sopcontroller/getSuppliersSop';
$route['getSupplierName'] = 'transactionControllers/sopcontroller/getSupplierName';
$route['getCustomersSop'] = 'transactionControllers/sopcontroller/getCustomersSop';
$route['checkUserTypeSOP'] = 'transactionControllers/sopcontroller/checkUserTypeSOP';
$route['loadVendorsDeal'] = 'transactionControllers/sopcontroller/loadVendorsDeal';
$route['loadSONos'] = 'transactionControllers/sopcontroller/loadSONos';
$route['loadSONosWoDeal'] = 'transactionControllers/sopcontroller/loadSONosWithoutDeal';
$route['loadDeductionType'] = 'transactionControllers/sopcontroller/loadDeductionType';
$route['loadDeduction'] = 'transactionControllers/sopcontroller/loadDeduction';
$route['forRegDiscount'] = 'transactionControllers/sopcontroller/calcAmountToBeDeductedForRegDisc';
$route['calculateDeduction'] = 'transactionControllers/sopcontroller/calculateDeduction';
$route['loadChargesType'] = 'transactionControllers/sopcontroller/loadChargesType';
$route['submitSOP'] = 'transactionControllers/sopcontroller/submitSOP';
$route['loadCwoSop'] = 'transactionControllers/sopcontroller/loadCwoSop';
$route['loadSopDetails'] = 'transactionControllers/sopcontroller/loadSopDetails';
$route['tagAsAudited'] = 'transactionControllers/sopcontroller/tagAsAudited';
$route['searchSOP'] = 'transactionControllers/sopcontroller/searchSOP';
$route['getDeductionOrder'] = 'transactionControllers/sopcontroller/getDeductionOrder';
$route['searchVar'] = 'transactionControllers/sopcontroller/searchVar';
$route['getSopStats'] = 'transactionControllers/sopcontroller/sopStatistics';



$route['getPOForSlip'] = 'transactionControllers/cwoslipcontroller/getPO/$1/$2';
// ============ TRANSACTIONS ============ //
$route['transactions/(:any)'] = 'baseController/transactions/$1';

// ============ REPORTS ============ //
$route['reports/(:any)'] = 'baseController/reports/$1';
$route['getIadReports'] = 'reportsController/iadReportController/getIadReports';
$route['getLedger/(:any)'] = 'reportsController/ledgerController/getLedger/$1';
$route['getDataLedger'] = 'reportsController/ledgerController/getDataLedger';
$route['getProformaHeader'] = 'reportsController/ledgerController/getProformaHeader';
$route['getDataLedgerInvoice'] = 'reportsController/ledgerController/getDataLedgerInvoice';

$route['getTransactionHistory'] = 'reportsController/POvProformaHistoryController/getTransactionHistory';
$route['generateProfvPiHistory'] = 'reportsController/ProformavPiHistoryController/generateProfvPiHistory';
$route['deleteprofvspi'] = 'reportsController/ProformavPiHistoryController/deleteprofvspi';
$route['generateProfvCrfHistory'] = 'reportsController/ProformavCrfHistoryController/generateProfvCrfHistory';
$route['deleteprofvscrf'] = 'reportsController/ProformavCrfHistoryController/deleteprofvscrf';
$route['generateSopHistory'] = 'transactionControllers/sopcontroller/generateSopHistory';
$route['generateDeductionReport'] = 'reportsController/DeductionReportController/generateDeductionReport';
$route['generateVarianceLedger'] = 'reportsController/varianceledgercontroller/generateVarianceLedger';
$route['getCrfDetails'] = 'reportsController/varianceledgercontroller/getCrfDetails';
$route['generatePoAgingReport'] = 'reportsController/poagingreportcontroller/generatePoAgingReport';

// ============ UTILITY ============ //
$route['utility/(:any)'] = 'baseController/utility/$1';
$route['generateUploadedTransaction'] = 'utilityController/UploadedTransactionController/generateUploadedTransaction';
$route['getSOPs'] = 'utilityController/ChangeSOPStatController/getSOPs';
$route['changeSOPStatus'] = 'utilityController/ChangeSOPStatController/changeSOPStatus';
$route['searchrecepient'] = 'utilityController/Messagecontroller/searchrecepient';
$route['sendmessage'] = 'utilityController/Messagecontroller/sendmessage';
$route['loadChatNotifperSender'] = 'utilityController/Messagecontroller/loadChatNotifperSender';
$route['displaythread'] = 'utilityController/Messagecontroller/displaythread';
$route['searchCRFVar'] = 'utilityController/Adjustmentcontroller/searchCRFVar';
$route['submitAdjustment'] = 'utilityController/Adjustmentcontroller/submitAdjustment';
$route['getAdjs'] = 'utilityController/Adjustmentcontroller/getAdjustments';
// $route['loadallrecepients']         = 'utilityController/Messagecontroller/loadallrecepients';
// $route['cancelSop']      = 'utilityController/ChangeSOPStatController/cancelSop'; 

# ADMIN ROUTES
$route['admin_login'] = 'AdminControllers/AdminController/admin_login';
$route['checkCredentials'] = 'AdminControllers/AdminController/checkCredentials';
$route['admin_home'] = 'AdminControllers/AdminController/admin_home';

# PORTAL ROUTES
$route['portal_login'] = 'PortalController/PortalController/portal_login';
$route['session_expire'] = 'baseController/session_expire';

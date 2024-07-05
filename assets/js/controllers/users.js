window.myApp.controller("users-controller", function ($scope, $http) {
  const thumbsupClass = '<i class="fas fa-thumbs-up"></i>';
  const thumbsdownClass = '<i class="fas fa-thumbs-down"></i>';

  $scope.userID = 0;
  $scope.retrieveoldpass = false;

  $scope.getUsers = () => {
    $http({
      method: "GET",
      url: $base_url + "getUsers",
    }).then(function successCallback(response) {
      $(document).ready(function () {
        $("#usersTable").DataTable();
      });
      $scope.users = response.data;
    });
  };

  $scope.saveUser = (e) => {
    e.preventDefault();

    var formData = {
      firstname: $scope.firstname,
      middlename: $scope.middlename,
      lastname: $scope.lastname,
      position: $scope.position,
      department: $scope.department,
      subsidiary: $scope.subsidiary,
      usertype: $scope.usertype,
      username: $scope.username,
      password: $scope.password,
    };

    Swal.fire({
      title: "Are you sure to proceed?",
      showCancelButton: true,
      showConfirmButton: true,
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      allowOutsideClick: false,
    }).then((result) => {
      if (result.dismiss != "cancel") {
        $http({
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
          },
          method: "POST",
          url: $base_url + "addUser",
          data: $.param(formData),
          responseType: "json",
        }).then(function successCallback(response) {
          if (response.data.info == "Success") {
            Swal.fire({
              icon: "success",
              title: "Success",
              text: response.data.message,
            }).then((result) => {
              location.reload();
            });
          } else if (response.data.info == "Error Saving") {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          } else if (response.data.info == "No Data") {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          } else if (response.info == "Duplicate") {
            Swal.fire({
              icon: "info",
              title: "Duplicate",
              text: response.data.message,
            });
          }
        });
      } else {
        Swal.close();
      }
    });
  };

  $scope.updateUser = (e) => {
    e.preventDefault();

    var formData = {
      ID: $scope.userID,
      usertype: $scope.usertype_u,
    };

    Swal.fire({
      title: "Are you sure to update user?",
      showCancelButton: true,
      showConfirmButton: true,
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      allowOutsideClick: false,
    }).then((result) => {
      if (result.dismiss != "cancel") {
        $http({
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
          },
          method: "POST",
          url: $base_url + "updateUser",
          data: $.param(formData),
          responseType: "json",
        }).then(function successCallback(response) {
          if (response.data.info == "Updated") {
            Swal.fire({
              icon: "success",
              title: "Success",
              text: response.data.message,
            }).then((result) => {
              location.reload();
            });
          } else if (response.data.info == "Error Saving") {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          } else if (response.data.info == "No Data") {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          }
        });
      } else {
        Swal.close();
      }
    });
  };

  $scope.deactivate = function (data) {
    var formData = { ID: data.user_id };
    Swal.fire({
      title: "Are you sure to deactivate user?",
      showCancelButton: true,
      showConfirmButton: true,
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      allowOutsideClick: false,
    }).then((result) => {
      if (result.dismiss != "cancel") {
        $http({
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
          },
          method: "POST",
          url: $base_url + "deactivate",
          data: $.param(formData),
          responseType: "json",
        }).then(function successCallback(response) {
          if (response.data.info == "Deactivated") {
            Swal.fire({
              icon: "success",
              title: "Success",
              text: response.data.message,
            }).then((result) => {
              location.reload();
            });
          } else if (response.data.info == "Error Deactivating") {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          } else if (response.data.info == "No ID") {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          }
        });
      } else {
        Swal.close();
      }
    });
  };

  $scope.getoldpass = () => {
    $scope.retrieveoldpass = false;
    $http({
      method: "POST",
      url: $base_url + "getoldpass",
      data: { oldpass: $scope.oldpass },
    }).then(function successCallback(response) {
      if (response.data == "same") {
        $scope.retrieveoldpass = true;
      }
    });
  };

  $scope.changepass = (e) => {
    e.preventDefault();

    var formData = { newpass: $scope.newpass };

    Swal.fire({
      icon: "warning",
      title: "Proceed to change your password?",
      showCancelButton: true,
      showConfirmButton: true,
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      allowOutsideClick: false,
    }).then((result) => {
      if (result.dismiss != "cancel") {
        $http({
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
          },
          method: "POST",
          url: $base_url + "changepassword",
          data: $.param(formData),
          responseType: "json",
        }).then(function successCallback(response) {
          if (response.data.info == "Success") {
            Swal.fire({
              icon: "success",
              title: "Success",
              html: response.data.message,
            }).then(function () {
              window.location = $base_url + "logout";
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          }
        });
      } else {
        Swal.close();
      }
    });
  };

  $scope.resetpassword = (data) => {
    var formData = { userid: data.user_id };

    Swal.fire({
      icon: "warning",
      title: "Reset password?",
      showCancelButton: true,
      showConfirmButton: true,
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      allowOutsideClick: false,
    }).then((result) => {
      if (result.dismiss != "cancel") {
        $http({
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
          },
          method: "POST",
          url: $base_url + "resetpassword",
          data: $.param(formData),
          responseType: "json",
        }).then(function successCallback(response) {
          if (response.data.info == "Success") {
            Swal.fire({
              icon: "success",
              title: "Success",
              html: response.data.message,
            }).then(function () {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          }
        });
      } else {
        Swal.close();
      }
    });
  };

  $scope.checkClass = () => {
    $scope.patternOk = $("#passwordclass .pattern-ok").length;
  };

  $scope.searchEmployee = (e) => {
    $(".search-emp-results").hide();
    $scope.searchResult = {};
    $scope.hasResults = 0;
    if ($scope.searchemployee == "") {
      $(".search-emp-results").hide();
      $scope.proceedApply = 0;
    } else {
      $http({
        method: "POST",
        url: $base_url + "searchEmp",
        data: { str: $scope.searchemployee },
      }).then(function successCallback(response) {
        $scope.searchResult = response.data;
        if ($scope.searchResult.length == 0) {
          $scope.hasResults = 0;
          $scope.proceedApply = 0;
          $scope.searchResult.push({ emp_id: "No Results Found" });
        } else {
          $scope.hasResults = 1;
          $scope.searchResult = response.data;
        }
      });
    }
  };

  $scope.addEmployee = (e, data) => {
    e.preventDefault();
    $(".search-emp-results").hide();
    $scope.searchemployee = "";
    $scope.proceedApply = 1;
    const employeeFullName = data.name;

    Swal.fire({
      icon: "question",
      html: `Are you sure to add this employee "<span style="color: #10a2e6; font-weight: bold;">${employeeFullName}</span>" as a user?`,
      showCancelButton: true,
      focusConfirm: false,
      confirmButtonText: thumbsupClass,
      confirmButtonAriaLabel: "Thumbs up!",
      cancelButtonText: thumbsdownClass,
      cancelButtonAriaLabel: "Thumbs down",
    }).then((result) => {
      if (result.isConfirmed) {
        $http({
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
          },
          method: "POST",
          url: $base_url + "addEmp",
          data: $.param(data),
          responseType: "json",
        }).then(function successCallback(response) {
          if (response.data.info == "Success") {
            Swal.fire({
              icon: "success",
              title: "Success",
              text: response.data.message,
            }).then(() => {
              location.reload();
            });
          } else if (response.data.info == "Error") {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          }
        });
      }
      // else {
      //     NoChangesDetected();
      // }
    });
  };

  $scope.getUserDetails = (data) => {
    $scope.userID = data.user_id;
    $scope.name_u = data.name;
    $scope.position_u = data.position;
    $scope.department_u = data.dep;
    $scope.company_u = data.comp;
    $scope.bu_u = data.bu;
    $scope.usertype_u = data.userType;
  };

  $scope.getOldUsername = () => {
    $http({
      method: "GET",
      url: $base_url + "getOldUsername",
    }).then(function successCallback(response) {
      $scope.olduser = response.data;
    });
  };

  $scope.changeusername = (e) => {
    e.preventDefault();

    var formData = { newuser: $scope.newuser };

    Swal.fire({
      icon: "warning",
      title: "Proceed to change your username?",
      showCancelButton: true,
      showConfirmButton: true,
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      allowOutsideClick: false,
    }).then((result) => {
      if (result.dismiss != "cancel") {
        $http({
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
          },
          method: "POST",
          url: $base_url + "changeUsername",
          data: $.param(formData),
          responseType: "json",
        }).then(function successCallback(response) {
          if (response.data.info == "Success") {
            Swal.fire({
              icon: "success",
              title: "Success",
              html: response.data.message,
            }).then(function () {
              window.location = $base_url + "logout";
            });
          } else if (response.data.info == "Info") {
            Swal.fire({
              icon: "info",
              title: "Info",
              text: response.data.message,
            }).then(function () {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data.message,
            });
          }
        });
      } else {
        Swal.close();
      }
    });
  };
});

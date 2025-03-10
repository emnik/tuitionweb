<!-- https://github.com/hgoebl/mobile-detect.js -->
<script src="<?php echo base_url('assets/mobile-detect.js/mobile-detect.min.js')?>"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-html5-1.6.4/b-print-1.6.4/fc-3.3.1/r-2.2.6/rg-1.1.2/sl-1.3.1/datatables.min.js"></script>

<!-- For date formating -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">

<script type="text/javascript">
  var oTable;
  var selectedIds = [];

  $(document).ready(function() {
    var md = new MobileDetect(window.navigator.userAgent);

    //Menu current active links and Title
    $('#menu-operation').addClass('active');
    $('#menu-teams').addClass('active');
    $('#menu-header-title').text('Microsoft Teams');

    $('#notify, #resend').popover({
      html: false,
      title: 'Ενεργοποίηση επιλογών ενημέρωσης χρήστη',
      content: "Συμπληρώστε τα πεδία τηλεφώνου (mobilePhone) και email (otherMails) στην καρτέλα 'Στοιχεία Χρήστη' για την ενεργοποίηση όλων των επιλογών ενημέρωσης χρήστη.",
      // container: 'body',
      placement: 'top',
      trigger: 'click'
    });


    function getHistory(){
      var id = selectedIds[0]; // Assuming the ID is in the first column
      console.log(id);
      $.ajax({
          url: '<?php echo base_url()?>teams/getMsgHistoryData',
          data: { id: id },
          method: 'POST',
          dataType: 'json',
          success: function(response) {
            console.log(response);
            if (response.status === 'error') {
              $('#info-empty-history').removeClass('hidden');
              $('#historyFields').addClass('hidden');
            } else {
              var data = response.data;
              console.log(data);
              $('#info-empty-history').addClass('hidden');
              $('#historyFields').removeClass('hidden');              
              // $('#messageDate').text(data.datetime ? data.datetime.split(' ')[0] : '');
              $('#messageDate').text(data.datetime);
              $('#lastMessage').val(data.message);
            }
          },
          error: function(xhr, status, error) {
              console.error('AJAX Error: ' + status + error);
          }
        });
    }

    $('a[href="#historyData"]').on('click', function() {
      $('#del-reg-modal').addClass('hidden');
      $('#submit').addClass('hidden');
      $('#reSendMsg').removeClass('hidden');
      getHistory();
    });

    $('a[href="#mainData"], a[href="#passwordData"]').on('click', function() {
      $('#del-reg-modal').removeClass('hidden');
      $('#submit').removeClass('hidden');
      $('#reSendMsg').addClass('hidden');
    });

    var dataTableOptions = {
      "language": {
          "paginate": {
              "first":    "Πρώτη",
              "previous": "",
              "next":     "",
              "last":     "Τελευταία"
          },
          "info": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_",
          "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
          // "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εγγραφές",
          "infoFiltered": "",
          "lengthMenu": "Εγγραφές/σελ. _MENU_",
          "loadingRecords": "Φόρτωση καταλόγου ...",
          "processing": "Επεξεργασία...",   
          "search": "Αναζήτηση:",
          "zeroRecords": '<button class="btn btn-primary" id="add-new">Δημιουργία νέου λογαριαμού</button>'
      }
    };

    // Custom sorting function for the select column
    $.fn.dataTable.ext.order['dom-checkbox'] = function(settings, col) {
      return this.api().column(col, { order: 'index' }).nodes().map(function(td, i) {
        return $('input[type="checkbox"]', td).prop('checked') ? '1' : '0';
      });
    };

    function whenNoRecord(action){
      if (action === 'add'){
        dataTableOptions.language.zeroRecords = '<button class="btn btn-primary" id="add-new">Δημιουργία νέου λογαριαμού</button>';
      } else {
        dataTableOptions.language.zeroRecords = '';
      }
    }

    $('#selectDataSrc').on('change', function() {
      // console.log('Selected value: ' + $(this).val());
      $('#select-all').trigger('change');
      $('#select-all').prop('checked', false);
      $('#select-all').prop('disabled', true);
      $('#student-card').removeClass('hidden');
      $('#restore').addClass('hidden');
      $('#warning-alert').addClass('hidden');
      $('#del-reg').addClass('hidden');
      $('#reset').html('<i class="icon-refresh"></i> Reset');
      var select = $(this).val();
      var url;
      if (select === 'alldata'){
        url = '<?php echo base_url()?>teams/getAllTeams';
        whenNoRecord('add');
      } else if (select === 'curStudents'){
        url = '<?php echo base_url()?>teams/getCurrentStudents';
        whenNoRecord('add');
      } else if (select === 'olderStudents'){
        whenNoRecord('empty');
        url = '<?php echo base_url()?>teams/getObsoleteUsers';
        $('#warning-msg').html('<strong>ΣΗΜΑΝΤΙΚΟ!</strong> Τα ακόλουθα δεδομένα βασίζονται στη σύγκριση των δεδομένων του Microsoft Teams με τους ενεργούς μαθητές και καθηγητές της επιλεγμένης διαχειριστικής περιόδου. <strong>Απαιτείται προσοχή στις διαγραφές</strong>!<br/>Μπορείτε να διαγράψετε ταυτόχρονα μέχρι <u>20 χρήστες</u>.');
        $('#warning-alert').removeClass('hidden');
        $('#del-reg').removeClass('hidden');
        //if on phone the reset button text is ommited!
        if(md.phone()!==null){
            $('#reset').html('<i class="icon-refresh"></i>');
        }
      } else if (select === 'curTeachers'){
        whenNoRecord('add');
        url = '<?php echo base_url()?>teams/getCurrentTeachers';
      } else if (select === 'deletedUsers'){
        whenNoRecord('empty');
        url = '<?php echo base_url()?>teams/getDeletedUsers';
        $('#warning-msg').html('<strong>ΣΗΜΑΝΤΙΚΟ!</strong> Οι διαγεγραμένοι λογαριασμοί μπορούν να ανακτηθούν μέσα σε χρονικό διάστημα 30 ημερών από την ημερομηνία διαγραφής! Παρακάτω είναι οι λογαριασμοί που μπορείτε να επαναφέρετε.');
        $('#warning-alert').removeClass('hidden');
        $('#student-card').addClass('hidden');
        $('#restore').removeClass('hidden');
      }
      getRemoteData(url);
    });

    function getRemoteData(selectedUrl){
      $.ajax({
        url: selectedUrl,
        method: 'GET',
        dataType: 'json',
        success: function(remoteData) {
            // console.log(remoteData);
            data=remoteData['data'];
            handleSuccess(data);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + error);
        }
      });
    }

    function handleSuccess(data) {
      if (oTable) {
        oTable.destroy();
        selectedIds = [];
      }
      oTable = $('#teamsTbl').DataTable({
        data: data,
        processing: true,
        responsive: true,
        responsive: {
          details: {
            type: 'column',
            // target: 'tr', //expand row when clicking anywhere in a row
            target: 'td:not(:first-child)' // Exclude the first column from the expandable area
          }
        },
        columns: [
          {
            data: "id",
            render: function(data, type, row) {
              return '<input type="checkbox" value="' + data + '">';
            },
            orderable: true,
            orderDataType: 'dom-checkbox'
          },
          { data: "displayName" },
          { data: "surname" },
          { data: "givenName" },
          { data: "mail" },
          { data: "id" },
          { data: "mobilePhone" },
          { data: "otherMails" }          
        ],
        order: [[2, 'asc']], // Sort by the "surname" column (index 1) in ascending order
        columnDefs: [
          { targets: [2, 3, 6, 7], visible: false }
        ],
        filter: true,
        paginate: true,
        language: {
          paginate: dataTableOptions.language.paginate,
          info: dataTableOptions.language.info,
          infoEmpty: dataTableOptions.language.infoEmpty,
          infoFiltered: dataTableOptions.language.infoFiltered,
          lengthMenu: dataTableOptions.language.lengthMenu,
          loadingRecords: dataTableOptions.language.loadingRecords,
          processing: dataTableOptions.language.processing,
          search: dataTableOptions.language.search,
          zeroRecords: dataTableOptions.language.zeroRecords
        }
      });
    }

    // Handle row selection
    $('#teamsTbl tbody').on('change', 'input[type="checkbox"]', function() {
      var id = $(this).val();
      if ($(this).is(':checked')) {
        if (selectDataSrc.value === 'olderStudents') {
          if (selectedIds.length >= 20) {
            alert('Batch selection is limited to 20 users');
            $(this).prop('checked', false);
          } else {
            selectedIds.push(id);
          }
        } else {
          if (selectedIds.length === 0) {
            $(this).prop('checked', true);
            selectedIds.push(id);
          } else {
            selectedIds = [id];
            $('#teamsTbl tbody input[type="checkbox"]').not(this).prop('checked', false);
          }
        }
      } else {
        selectedIds = selectedIds.filter(function(value) {
          return value != id;
        });
      }

      // Update "Select All" checkbox state
      var anyChecked = selectedIds.length > 0;
      $('#select-all').prop('checked', anyChecked);
      $('#select-all').prop('disabled', !anyChecked);
    });


    // Handle "Select All" checkbox
    $('#select-all').on('change', function() {
        var rows = oTable.rows().nodes(); // Get all rows of the table
        $('input[type="checkbox"]', rows).prop('checked', false);
        selectedIds = [];
        $('#select-all').prop('disabled', true);
    });

    // Initially disable the "Select All" checkbox
    $('#select-all').prop('disabled', true);

    /* Add a click handler for the student-card btn */
    $('#student-card').click(function() {
      if (selectedIds.length === 1) {
      var id = selectedIds[0]; // Assuming the ID is in the first column
      var rowData = oTable.row($('input[value="' + id + '"]').closest('tr')).data();
      
      // Populate the modal form fields with the row data
      $('#userId').val(rowData.id);
      $('#surname').val(rowData.surname);
      $('#givenName').val(rowData.givenName);
      $('#mail').val(rowData.mail);
      $('#displayName').val(rowData.displayName);
      $('#mobilePhone').val(rowData.mobilePhone);
      if (rowData.otherMails) {
        var otherMailsJson = JSON.parse(rowData.otherMails);
        $('#otherMails').val(Array.isArray(otherMailsJson) ? otherMailsJson.join(', ') : rowData.otherMails);
      } else {
        $('#otherMails').val('');
      }
      $('#password').val(''); // Clear the password field
      $('#forceChangePasswordNextSignIn').prop('checked', false);
      toggleEmailRadios();
      toggleSMSRadios();


      if(md.phone()===null){
          $('#notifyUserViaSMS').closest('.radio').addClass('hidden');
          $('#resendMsgViaSMS').closest('.radio').addClass('hidden');
      }

      $('#myModal').modal('show');
      } else {
      alert("Για τη συγκεκριμένη ενέργεια πρέπει να έχετε επιλέξει ένα χρήστη!");
      }
    });

    $('#reset').click(function() {
      $('body').css('cursor', 'wait');
      $.ajax({
        url: '<?php echo base_url()?>teams/resetTeamsData',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $('body').css('cursor', 'default');
            console.log(data);
            if (data.status === 'success') {
              alert('Τα δεδομένα των Microsoft Teams λήφθηκαν επιτυχώς.');
              $('#selectDataSrc').val('alldata').trigger('change');
            } else {
              alert('Προέκυψε σφάλμα κατά την επαναφορά των δεδομένων των Microsoft Teams.');
              console.error('Error: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            $('body').css('cursor', 'default');
            alert('Προέκυψε σφάλμα κατά την επαναφορά των δεδομένων των Microsoft Teams.');
            console.error('AJAX Error: ' + status + error);
        }
      });
    });

    /* Add a click handler for the new-reg btn */
    $('#new-reg').click(function() {
      // window.open('student/newreg', '_self', false);
    });

    /* Add a click handler for the del-reg btn */
    $('#del-reg, #del-reg-modal').click(function() {
      // console.log(selectedIds);
      if (selectedIds.length > 0) {
        var r = confirm("Οι χρήστες που επιλέξατε πρόκειται να διαγραφούν. Παρακαλώ επιβεβαιώστε.");
        if (r == true) {
          $('body').css('cursor', 'wait');
          $.ajax({
            url: '<?php echo base_url()?>teams/batchDeleteUsers',
            method: 'POST',
            data: { data: selectedIds },
            dataType: 'json',
            success: function(data) {
                $('body').css('cursor', 'default');
                console.log(data);
                if (data.status === 'success') {
                  if ($('#myModal').hasClass('in')) {
                    $('#myModal').modal('hide');
                  }
                  $('#selectDataSrc').val('alldata').trigger('change');
                  $('#select-all').prop('checked', false);
                  $('#select-all').prop('disabled', true);
                  alert('Οι χρήστες διαγράφηκαν επιτυχώς.');
                } else {
                  alert('Προέκυψε σφάλμα κατά τη διαγραφή των χρηστών.');
                  console.error('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                $('body').css('cursor', 'default');
                alert('Προέκυψε σφάλμα κατά τη διαγραφή των χρηστών.');
                console.error('AJAX Error: ' + status + error);
            }
          });          
          // alert('Deleting users with IDs: ' + selectedIds.join(', '));
        }
      } else {
        alert("Δεν έχετε επιλέξει κανένα χρήστη.");
      }
    });

    // On load get all the data
    getRemoteData('<?php echo base_url()?>teams/getAllTeams');


    function getStudentLocalData(surname, givenName){
      // get the Phone for the current user from the local database - only if it is a user of the current selected school year
      // This is usefull when the phone number is not available in the Microsoft Teams data or it has been changed!
      // TODO: Update this to also get the student's mail when stored in the database
      $.ajax({
        url: '<?php echo base_url()?>teams/getStudentLocalData',
        method: 'POST',
        data: { surname: surname, givenName: givenName },
        dataType: 'json',
        success: function(rdata) {
            console.log(rdata);
            if (rdata.status === 'success') {
              var mobilePhone = rdata.data.std_mobile;
              if (mobilePhone && !mobilePhone.startsWith('+30')) {
                mobilePhone = '+30' + mobilePhone;
              }
              if (mobilePhone.length === 13) {
                $('#mobilePhone').val(mobilePhone);
                $('#localDataMessage').text('Το τηλέφωνο ενημερώθηκε από τη βάση δεδομένων του φροντιστηρίου.');
                $('#localDataMessage').removeClass('hidden');
               } else {
                $('#mobilePhone').val('');
                $('#localDataMessage').text('Το διαθέσιμο τηλέφωνο αφαιρέθηκε λόγω λανθασμένου αριθμού ψηφίων.');
                $('#localDataMessage').removeClass('hidden');
               }
            } else {
              console.error('Error: ' + rdata.message);
              $('#localDataMessage').addClass('hidden');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + error);
        }
      });
    }

// -------------------Modal-----------------------

var initialFormData = {};
  
  // Store initial form data when the modal is shown
  $('#myModal').on('show.bs.modal', function() {
    $('a[href="#mainData"]').tab('show');
    $('#del-reg-modal').removeClass('hidden');
    $('#submit').removeClass('hidden');
    $('#reSendMsg').addClass('hidden');
    initialFormData = {};
    $('#updateUserForm').find('input').each(function() {
      var name = $(this).attr('name');
      if ($(this).is(':checkbox')) {
        initialFormData[name] = $(this).is(':checked') ? true : false;
      } 
      else if ($(this).is(':radio')) {
        // Exclude radio buttons from initial form data
      } else {
        initialFormData[name] = $(this).val();
      }
    });
    // console.log(initialFormData);
    getStudentLocalData(initialFormData['surname'], initialFormData['givenName']);
  });

  // Handle form submission
  $('#submit').on('click', function() {
    var alteredData = {};
    $('#updateUserForm').find('input').each(function() {
      var name = $(this).attr('name');
      var value;
      if ($(this).is(':checkbox')) {
        value = $(this).is(':checked') ? true : false;
        if ($('#password').val() !== '' && $('#password').val() !== initialFormData['password']) {
          alteredData[name] = value; // Include checkbox values conditionally
        }
      } else if ($(this).is(':radio')) {
        // Exclude radio buttons from initial form data
      } else {
        value = $(this).val();
        if (initialFormData[name] !== value) {
          // console.log(name + ': ' + initialFormData[name] + ' -> ' + value);
          alteredData[name] = value;
        }
      }
    });

    alteredData['userId'] = $('#userId').val(); // Ensure the userId is always included

    // console.log(alteredData);

    if (Object.keys(alteredData).length === 1 && alteredData.hasOwnProperty('userId')) {
      alert('Δεν έχουν γίνει αλλαγές στα δεδομένα.');
      return;
    }
     
    $.ajax({
      url: '<?php echo base_url()?>teams/updateUser',
      method: 'POST',
      data: alteredData,
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          alert('Η ενημέρωση ήταν επιτυχης!');
          // notify user
          var email = $('#mail').val();
          if ($('#notifyUserViaPersonalEmail').is(':checked') && $('#password').val() !== '') {
            var mailtoLink = 'mailto:';
            var otherMails = $('#otherMails').val();
            if (otherMails.trim() !== '') {
              mailtoLink += otherMails.split(',')[0]; // Use the first email in the list
            }
            mailtoLink += '?subject=Microsoft Teams - Password resetted&body=Ο ΔΙΑΧΕΙΡΙΣΤΗΣ ΕΧΕΙ ΕΠΑΝΑΦΕΡΕΙ ΤΟΝ ΚΩΔΙΚΟ ΠΡΟΣΒΑΣΗΣ ΤΟΥ ΧΡΗΣΤΗ ' + $('#mail').val() + ' ΣΕ: ' + $('#password').val();
            window.open(mailtoLink);
            var id=$('#userId').val();
            saveMsgToDb(id, 'Ο ΔΙΑΧΕΙΡΙΣΤΗΣ ΕΧΕΙ ΕΠΑΝΑΦΕΡΕΙ ΤΟΝ ΚΩΔΙΚΟ ΠΡΟΣΒΑΣΗΣ ΤΟΥ ΧΡΗΣΤΗ ' + $('#mail').val() + ' ΣΕ: ' + $('#password').val());
          } else if ($('#notifyUserViaEmail').is(':checked') && $('#password').val() !== '') {
            var email_address = $('#otherMails').val();
            if (email_address.trim() !== '') {
              email_address = email_address.split(',')[0]; // Use the first email in the list
            }
            var email_body = 'Ο ΔΙΑΧΕΙΡΙΣΤΗΣ ΕΧΕΙ ΕΠΑΝΑΦΕΡΕΙ ΤΟΝ ΚΩΔΙΚΟ ΠΡΟΣΒΑΣΗΣ ΤΟΥ ΧΡΗΣΤΗ ' + $('#mail').val() + ' ΣΕ: ' + $('#password').val();
            var email_subject= 'Microsoft Teams - Password resetted';
            $.ajax({
                url: 'teams/send_single_email',
                type: 'POST',
                data: {
                    email_address: email_address,
                    email_body: email_body,
                    email_subject: email_subject
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert('Το email στάλθηκε επιτυχώς!');
                        var id=$('#userId').val();
                        saveMsgToDb(id, email_body);
                    } else {
                        alert('Προέκυψε σφάλμα κατά την αποστολή του email: ' + result.message);
                    }
                },
                error: function() {
                    alert('Προέκυψε σφάλμα κατά την αποστολή του email');
                }
            });
          } else if ($('#notifyUserViaSMS').is(':checked') && $('#password').val() !== '') {
            var phoneSMS = 'sms:';
            if ($('#mobilePhone').val()) {
              phoneSMS += $('#mobilePhone').val();
            }
            phoneSMS += '?body=Ο ΔΙΑΧΕΙΡΙΣΤΗΣ ΕΧΕΙ ΕΠΑΝΑΦΕΡΕΙ ΤΟΝ ΚΩΔΙΚΟ ΠΡΟΣΒΑΣΗΣ ΤΟΥ ΧΡΗΣΤΗ ' + $('#mail').val() + ' ΣΕ: ' + $('#password').val();
            window.open(phoneSMS);
            var id=$('#userId').val();
            saveMsgToDb(id, 'Ο ΔΙΑΧΕΙΡΙΣΤΗΣ ΕΧΕΙ ΕΠΑΝΑΦΕΡΕΙ ΤΟΝ ΚΩΔΙΚΟ ΠΡΟΣΒΑΣΗΣ ΤΟΥ ΧΡΗΣΤΗ ' + $('#mail').val() + ' ΣΕ: ' + $('#password').val());
          } else if ($('#notifyUserViaSMSto').is(':checked') && $('#password').val() !== '') {
            var to = $('#mobilePhone').val();
            var message = 'Ο ΔΙΑΧΕΙΡΙΣΤΗΣ ΕΧΕΙ ΕΠΑΝΑΦΕΡΕΙ ΤΟΝ ΚΩΔΙΚΟ ΠΡΟΣΒΑΣΗΣ ΤΟΥ ΧΡΗΣΤΗ ' + $('#mail').val() + ' ΣΕ: ' + $('#password').val();
            $.ajax({
              url: '<?php echo base_url()?>teams/sendUsingSMSto',
              method: 'POST',
              data: { to: to, message: message },
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  alert('Το μήνυμα SMS στάλθηκε επιτυχώς!');
                  var id=$('#userId').val();
                  saveMsgToDb(id, message);
                } else {
                  alert('Προέκυψε σφάλμα κατά την αποστολή του μηνύματος SMS: ' + response.message);
                }
              },
              error: function(xhr, status, error) {
                alert('AJAX Error: ' + status + error);
              }
            });
          }
        } else {
          alert('Προέκυψε σφάλμα κατά την ενημέρωση του χρήστη: ' + response.message);
        }
        // Update initialFormData with alteredData
        for (var key in alteredData) {
          if (alteredData.hasOwnProperty(key)) {
            initialFormData[key] = alteredData[key];
          }
        }
        // console.log(initialFormData);
      },
      error: function(xhr, status, error) {
        alert('AJAX Error: ' + status + error);
      }
    });
  });


  $('#reSendMsg').on('click', function(){
    var email = $('#mail').val();
    if ($('#resendMsgViaPersonalEmail').is(':checked') && $('#lastMessage').val() !== '') {
        var mailtoLink = 'mailto:';
        var otherMails = $('#otherMails').val();
        if (otherMails.trim() !== '') {
            mailtoLink += otherMails.split(',')[0]; // Use the first email in the list
        }
        mailtoLink += '?subject=Microsoft Teams - Password resetted&body=' + $('#lastMessage').val();
        window.open(mailtoLink);
        var id = $('#userId').val();
        saveMsgToDb(id, $('#lastMessage').val(), getHistory);
    } else if ($('#resendMsgViaEmail').is(':checked') && $('#lastMessage').val() !== '') {
        var email_address = $('#otherMails').val();
        if (email_address.trim() !== '') {
            email_address = email_address.split(',')[0]; // Use the first email in the list
        }
        var email_body = $('#lastMessage').val();
        var email_subject= 'Microsoft Teams - Password resetted';
        $.ajax({
            url: 'teams/send_single_email',
            type: 'POST',
            data: {
                email_address: email_address,
                email_body: email_body,
                email_subject: email_subject
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success') {
                    alert('Το email στάλθηκε επιτυχώς!');
                    var id=$('#userId').val();
                    saveMsgToDb(id, email_body, getHistory);
                } else {
                    alert('Προέκυψε σφάλμα κατά την αποστολή του email: ' + result.message);
                }
            },
            error: function() {
                alert('An error occurred while sending the email.');
            }
        });
    } else if ($('#resendMsgViaSMS').is(':checked') && $('#lastMessage').val() !== '') {
        var phoneSMS = 'sms:';
        if ($('#mobilePhone').val()) {
            phoneSMS += $('#mobilePhone').val();
        }
        phoneSMS += '?body=' + $('#lastMessage').val();
        window.open(phoneSMS);
        var id=$('#userId').val();
        saveMsgToDb(id, $('#lastMessage').val(), getHistory);
    } else if ($('#resendMsgViaSMSto').is(':checked') && $('#lastMessage').val() !== '') {
        var to = $('#mobilePhone').val();
        var message = $('#lastMessage').val();
        $.ajax({
            url: '<?php echo base_url()?>teams/sendUsingSMSto',
            method: 'POST',
            data: { to: to, message: message },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Το μήνυμα SMS στάλθηκε επιτυχώς!');
                    var id=$('#userId').val();
                    saveMsgToDb(id, message, getHistory);
                } else {
                    alert('Προέκυψε σφάλμα κατά την αποστολή του μηνύματος SMS: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + status + error);
            }
        });
    }
  });

  // Reset form data when the modal is closed
  $('#myModal').on('hidden.bs.modal', function() {
    $('#updateUserForm')[0].reset();
    $('#messageDate').text(''); // Clear the content of the span element
    var curDataSrc = $('#selectDataSrc').val();
    $('#selectDataSrc').val(curDataSrc).trigger('change');
    // $('#select-all').trigger('change');
    // $('#select-all').prop('checked', false);
    // $('#select-all').prop('disabled', true);
  });

  // Function to store the message sent to the database
  function saveMsgToDb(id, msg, callback){
    $.ajax({
        url: '<?php echo base_url()?>teams/saveMessageToHistory',
        method: 'POST',
        data: { id: id, message_body: msg },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                console.log('Το μήνυμα αποθηκεύτηκε επιτυχώς!');
                if (callback) callback();
            } else {
                console.log('Προέκυψε σφάλμα κατά την αποθήκευση του μηνύματος: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('AJAX Error: ' + status + error);
        }
    });
  }

  // Function to enable SMS radio buttons if mobilePhone is not empty
  function toggleSMSRadios() {
    var mobilePhone = $('#mobilePhone').val();
    if (mobilePhone.trim() !== '') {
      $('#notifyUserViaSMSto').prop('disabled', false);
      $('#notifyUserViaSMS').prop('disabled', false);
      $('#resendMsgViaSMSto').prop('disabled', false);
      $('#resendMsgViaSMS').prop('disabled', false);
    } else {
      $('#notifyUserViaSMSto').prop('disabled', true);
      $('#notifyUserViaSMSto').prop('checked', false);
      $('#resendMsgViaSMSto').prop('disabled', true);
      $('#resendMsgViaSMSto').prop('checked', false);
      // I don't disable the notifyUserViaSMS and resendMsgViaSMS radio buttons because it will be shown only
      // on mobile devices where the user can input the number manually
      $('#notifyUserViaSMS').prop('disabled', true);
      $('#notifyUserViaSMS').prop('checked', false);
      $('#resendMsgViaSMS').prop('disabled', true);
      $('#resendMsgViaSMS').prop('checked', false);
    }
  }

  // Bind the change event to the mobilePhone input field
  $('#mobilePhone').on('input', toggleSMSRadios);

  // Function to enable email radio buttons if otherMails is not empty
  function toggleEmailRadios() {
    var otherMails = $('#otherMails').val();
    if (otherMails.trim() !== '') {
      $('#notifyUserViaEmail').prop('disabled', false);
      $('#notifyUserViaPersonalEmail').prop('disabled', false);
      $('#resendMsgViaEmail').prop('disabled', false);
      $('#resendMsgViaPersonalEmail').prop('disabled', false);
    } else {
      $('#notifyUserViaEmail').prop('disabled', true);
      $('#notifyUserViaEmail').prop('checked', false);
      $('#resendMsgViaEmail').prop('disabled', true);
      $('#resendMsgViaEmail').prop('checked', false);
      // I don't disable the notifyUserViaPersonalEmail and resendMsgViaPersonalEmail radio buttons because it will
      // trigger the devices email client where the user can input the email manually
      $('#notifyUserViaPersonalEmail').prop('disabled', true);
      $('#notifyUserViaPersonalEmail').prop('checked', false);
      $('#resendMsgViaPersonalEmail').prop('disabled', true);
      $('#resendMsgViaPersonalEmail').prop('checked', false);
    }
  }

  // Bind the change event to the otherMails input field
  $('#otherMails').on('input', toggleEmailRadios);

  // ------------ Password generation code -----------------
  // Function to generate a random password
  function generateRandomPassword(length) {
    var charset = {
      upper: "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
      lower: "abcdefghijklmnopqrstuvwxyz",
      number: "0123456789"
      // symbol: "!@#$%^&*()_+~`|}{[]:;?><,./-="
    };
    var allChars = charset.upper + charset.lower + charset.number + charset.symbol;
    var password = "";

    // Ensure the password includes at least one character from three of the four sets
    password += charset.upper.charAt(Math.floor(Math.random() * charset.upper.length));
    password += charset.lower.charAt(Math.floor(Math.random() * charset.lower.length));
    password += charset.number.charAt(Math.floor(Math.random() * charset.number.length));
    // password += charset.symbol.charAt(Math.floor(Math.random() * charset.symbol.length));

    for (var i = 3; i < length; ++i) {// i = 4 if symbols are included
      password += allChars.charAt(Math.floor(Math.random() * allChars.length));
    }

    // Shuffle the password to ensure randomness
    password = password.split('').sort(function() { return 0.5 - Math.random() }).join('');

    return password;
  }

  // Bind the generate password button click event
  $('#generatePassword').on('click', function() {
    var passwordLength = 8; // Set the desired password length here
    var newPassword = generateRandomPassword(passwordLength); // Generate a password with the specified length
    $('#password').val(newPassword); // Set the generated password in the input field
    $('#password').trigger('input'); // Trigger input event to check complexity
  });  

  // Function to check password complexity
  function checkPasswordComplexity(password) {
    var hasUppercase = false;
    var hasLowercase = false;
    var hasNumbers = false;
    var hasSymbols = false;

    for (var i = 0; i < password.length; i++) {
      var char = password.charAt(i);
      if (/[A-Z]/.test(char)) {
        hasUppercase = true;
      } else if (/[a-z]/.test(char)) {
        hasLowercase = true;
      } else if (/[0-9]/.test(char)) {
        hasNumbers = true;
      } else if (/[!@#$%^&*()_+~`|}{[\]:;?><,./-=]/.test(char)) {
        hasSymbols = true;
      }
    }

    var metRequirements = 0;
    if (hasUppercase) metRequirements++;
    if (hasLowercase) metRequirements++;
    if (hasNumbers) metRequirements++;
    if (hasSymbols) metRequirements++;

    console.log('Met requirements: ' + metRequirements);
    if (password.length < 8) {
      return false;
    }
    return metRequirements >= 3;
  }

  // Check password complexity on input
  $('#password').on('input', function() {
    var password = $(this).val();
    if (password === "") {
      $('#password-complexity-message').hide();
      return;
    }
    var isValid = checkPasswordComplexity(password);
    if (!isValid) {
      $('#password-complexity-message').text('Ο κωδικός πρέπει να περιλαμβάνει τουλάχιστον τρία από τα εξής: Κεφαλαία γράμματα (A-Z), Μικρά γράμματα (a-z), Αριθμούς (0-9), Σύμβολα (π.χ., @, #, $, κ.λπ.)  και να έχει μήκος τουλάχιστον 8 χαρακτήρες.').show();
    } 
    else {
      $('#password-complexity-message').hide();
    }
  });

  // Hide the password complexity message initially
  $('#password-complexity-message').hide();

  }); //end of document(ready) function


</script>


</head>

<body>
  <div class="wrapper">

    <!-- Menu start -->
    <?php include __DIR__ . '/include/menu.php'; ?>
    <!-- Menu end -->

    <!-- main container -->
    <div class="container" style="padding-top:10px; padding-bottom:70px;">
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url(); ?>"><i class="icon-home"> </i> Αρχική</a></li>
          <li class="active">Microsoft Teams</li>
        </ul>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="icon">
            <i class="icon-windows"></i>
          </span>
          <h3 class="panel-title">Microsoft Teams</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <?php if ($configGraphAPI === 'error'):?>
                <div class="alert alert-danger">
                  <strong>Σφάλμα!</strong> Δεν ήταν δυνατή η σύνδεση με το Microsoft Graph API. Παρακαλώ ελέγξτε τις <a href="<?php echo base_url('contact_config'); ?>">ρυθμίσεις σύνδεσης</a>.
                </div>
                <?php else:?>
                <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
                  <button class="btn btn-sm hidden btn-danger pull-left" id="del-reg"><i class="icon-trash"></i></button>
                  <div class="btn-group pull-left">
                    <button class="btn btn-default btn-sm" id="reset" data-toggle="tooltip" title="Ανάκτηση δεδομένων από τον διακομιστή του Microsoft Teams"><i class="icon-refresh"></i> Reset</button>
                    <button class="btn btn-default btn-sm" id="new-reg"><i class="icon-plus"></i></button>
                  </div>
                  <div class="pull-left col-md-3 col-xs-5">
                    <select id="selectDataSrc" class="form-control input-sm">
                      <option value="alldata">Εμφάνιση όλων</option>
                      <option value="curStudents">Ενεργοί μαθητές</option>
                      <option value="curTeachers">Ενεργοί καθηγητές</option>
                      <option value="olderStudents">Παρωχημένοι λογαριασμοί</option>
                      <hr>
                      <option value="deletedUsers">Διαγραμμένοι χρήστες</option>
                    </select>
                <div>
              </div>
            </div>
            <button class="btn btn-openpage btn-sm pull-right" id="student-card"><i class="icon-user"> </i>Επεξεργασία</button>
            <button class="btn btn-openpage btn-sm pull-right hidden" id="restore"><i class="icon-user"> </i>Επαναφορά</button>
          </div>
          <div class="alert alert-danger alert-dismissable hidden" id="warning-alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <span id="warning-msg"></span>
          </div>
          <table class="table table-striped table-bordered" id="teamsTbl" width="100%">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Display Name</th>
                <th>Επώνυμο</th>
                <th>Όνομα</th>
                <th>Username</th>
                <th>Teams user id</th>
                <th>mobilePhone</th>
                <th>otherMails</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
          <?php endif;?>
        </div> <!-- end of content -->
      </div> <!-- end of contentbox -->

    </div>
    
    </div>
    </div>
    <!--end of main container-->

    <div class="push"></div>
  </div> <!-- end of body wrapper-->

  <!-- Modal -->
  <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Επεξεργασία Χρήστη </h3>
        </div>
        <div class="modal-body">
          <form id="updateUserForm">
            <ul class="nav nav-tabs" role="tablist">
              <li class="active"><a href="#mainData" role="tab" data-toggle="tab">Στοιχεία Χρήστη</a></li>
              <li><a href="#passwordData" role="tab" data-toggle="tab">Κωδικός</a></li>
              <li><a href="#historyData" role="tab" data-toggle="tab">Ιστορικό</a></li>
            </ul>
            <div class="tab-content" style="padding-top:10px;">
              <div class="tab-pane active" id="mainData">
                <div class="row">
                  <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                      <label for="userId">Teams User ID</label>
                      <input type="text" class="form-control" id="userId" name="userId" readonly>
                    </div>
                    <div class="form-group">
                      <label for="surname">Επώνυμο</label>
                      <input type="text" class="form-control" id="surname" name="surname" required>
                    </div>
                    <div class="form-group">
                      <label for="givenName">Όνομα</label>
                      <input type="text" class="form-control" id="givenName" name="givenName" required>
                    </div>
                    <div class="form-group">
                      <label for="displayName">Display Name</label>
                      <input type="text" class="form-control" id="displayName" name="displayName" required>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                      <label for="mail">Mail / Username</label>
                      <input type="email" class="form-control" id="mail" name="mail" readonly>
                    </div>
                    <div class="form-group">
                      <label for="otherMails">Other Emails</label>
                      <input type="text" class="form-control" id="otherMails" name="otherMails">
                    </div>
                    <div class="form-group">
                      <label for="mobilePhone">Mobile Phone</label>
                      <input type="text" class="form-control" id="mobilePhone" name="mobilePhone">
                      <small id="localDataMessage" class="text-muted hidden"></small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="passwordData">
                <div class="row">
                  <div class="col-xs-12">
                    <div class="form-group">
                      <label for="password">New Password</label>
                      <div class="input-group">
                      <input type="text" class="form-control" id="password" name="password">
                      <span class="input-group-btn">
                        <button class="btn btn-primary" id="generatePassword" type="button">Generate</button>
                      </span>
                      </div>
                      <small id="password-complexity-message" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" id="forceChangePasswordNextSignIn" name="forceChangePasswordNextSignIn" /> Force Change Password Next Sign-In
                        </label>
                      </div>
                    </div>
                    </form>
                    <div class="form-group">
                    <label>Ενημέρωση Χρήστη <span><i id="notify" class="icon icon-info-sign"></i></span></label>
                    <div class="radio"  style="margin-top: 0px;">
                      <label>
                        <input type="radio" id="notifyUserViaPersonalEmail" value="notifyUserViaPersonalEmail" name="notifyUser"> με Email (μέσω προσωπικού email)
                      </label>
                      </div>
                      <div class="radio">
                      <label>
                        <input type="radio" id="notifyUserViaEmail" value="notifyUserViaEmail" name="notifyUser"> με Email (μέσω εταιρικού email)
                      </label>
                      </div>                      
                      <?php if($configSMS === 'success'):?>
                      <div class="radio">
                      <label>
                        <input type="radio" id="notifyUserViaSMSto" value="notifyUserViaSMSto" name="notifyUser"> με SMS (μέσω υπηρεσίας SMS.to)
                      </label>
                      </div>
                      <?php endif;?>
                      <div class="radio">
                      <label>
                        <input type="radio" id="notifyUserViaSMS" value="notifyUserViaSMS" name="notifyUser"> με SMS (μέσω κινητού τηλεφώνου)
                      </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="historyData">
                <div class="row">
                  <div class="col-xs-12">
                    <div class="alert alert-info hidden" id="info-empty-history">
                      <i class="icon icon-calendar-empty"></i><span style="padding-left:10px;">Δεν υπάρχει ιστορικό μηνυμάτων για αυτόν τον χρήστη.</span>
                    </div>
                    <div id="historyFields">
                      <div class="form-group">
                        <label for="lastMessage">Τελευταίο μήνυμα</label>
                        <div class="input-group">
                          <span class="input-group-addon" id='messageDate' readonly></span>
                          <textarea class="form-control" id="lastMessage" name="lastMessage" rows="3" readonly></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                      <label>Επαναποστολή μηνύματος <span><i id="resend" class="icon icon-info-sign"></i></span></label>
                      <div class="radio"  style="margin-top: 0px;">
                        <label>
                          <input type="radio" id="resendMsgViaPersonalEmail" value="resendMsgViaPersonalEmail" name="resendMsg"> με Email (μέσω προσωπικού email)
                        </label>
                        </div>
                        <div class="radio">
                        <label>
                          <input type="radio" id="resendMsgViaEmail" value="resendMsgViaEmail" name="resendMsg"> με Email (μέσω εταιρικού email)
                        </label>
                        </div>                      
                        <?php if($configSMS === 'success'):?>
                        <div class="radio">
                        <label>
                          <input type="radio" id="resendMsgViaSMSto" value="resendMsgViaSMSto" name="resendMsg"> με SMS (μέσω υπηρεσίας SMS.to)
                        </label>
                        </div>
                        <?php endif;?>
                        <div class="radio">
                        <label>
                          <input type="radio" id="resendMsgViaSMS" value="resendMsgViaSMS" name="resendMsg"> με SMS (μέσω κινητού τηλεφώνου)
                        </label>
                        </div>
                      </div>
                    </div>
                  </div>                    
                  </div>
                </div>
              </div>
            </div>
          <!-- </form> -->
          <div class="modal-footer" >
            <button class="btn btn-default btn-danger pull-left" id="del-reg-modal">Διαγραφή</button>
            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Κλείσιμο</button>
            <button id="submit" class="btn btn-primary">Αποθήκευση</button>
            <button id="reSendMsg" class="btn btn-primary hidden">Επαναποστολή</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
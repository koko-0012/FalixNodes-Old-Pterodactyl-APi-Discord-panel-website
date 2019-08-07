<?php
echo '
	<footer class="pt-4 my-md-5 pt-md-5 border-top">
        <div class="row">
          <div class="col-12 col-md">
			<h5>About Us</h5>
            LimitedNodes were founded by CsgoIsBae,  koko and nathan in may the 27th 2019.<br />
			The first hosting provider which provides anyone the powerful services for free.
            <small class="d-block mb-3 text-muted">&copy; 2018-2019</small>
          </div>
          <div class="col-6 col-md">
            <h5>Knowledge / Wiki</h5>
            <ul class="list-unstyled text-small">
              <li><a class="text-muted" href="http://example.com/wiki/index.php?title=Main_Page">View the knowledge / wiki</a></li>
            </ul>
          </div>
          <div class="col-6 col-md">
            <h5>Legal</h5>
            <ul class="list-unstyled text-small">
              <li><a class="text-muted" href="#">Terms of Services</a></li>
              <li><a class="text-muted" href="#">Privacy Policy</a></li>
              <li><a class="text-muted" href="#">Refund Policy</a></li>
            </ul>
          </div>
          <div class="col-6 col-md">
            <h5>Important Links</h5>
            <ul class="list-unstyled text-small">
              <li><a class="text-muted" href="#" role="button" data-toggle="modal" data-target="#hardware_info">Hardware List</a></li>
              <li><a class="text-muted" href="#" role="button" data-toggle="modal" data-target="#staffs_list">Staffs List</a></li>
            </ul>
          </div>
        </div>
      </footer>
	  
	<!-- modal:hardware_info -->
	<div id="hardware_info" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Hardware List</h4>
		  </div>
		  <div class="modal-body">
			<strong>Free Customers</strong><br />
			Node-2
			CPU » Intel(R) Xeon(R) CPU 2 x Gold 6132 CPU @ 2.60GHz<br />
			RAM » 24 GB DDR4<br />
			DISK » 240 GB SSD
			Cores » 6
			Node-4
			CPU » Intel(R) Xeon(R) CPU 2 x Gold 6132 CPU @ 2.60GHz<br />
			RAM » 16 GB DDR4<br />
			DISK » 160 GB SSD
			Cores » 4
			<br /><br />
			<strong>Paid Customers</strong><br />
			Node-1
			CPU » Intel(R) Xeon(R) CPU 2 x Gold 6132 CPU @ 2.60GHz<br />
			RAM » 32 GB DDR4<br />
			DISK » 320GB NVMe SSD RAID 1
			Cores » 8
			Node-3
			CPU » Intel(R) Xeon(R) CPU 2 x Gold 6132 CPU @ 2.60GHz<br />
			RAM » 32 GB DDR4<br />
			DISK » 320GB NVMe SSD RAID 1
			Cores » 8
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
	
	<!-- modal:staffs_list -->
	<div id="staffs_list" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Staffs List</h4>
		  </div>
		  <div class="modal-body">
			<strong>CsgoIsbae</strong><br />
			Real Name: CSGO<br />
			Position: Founder<br />
			Discord: CsgoIsBae#5091
			<br /><br />
			<strong>koko</strong><br />
			Real Name: koko<br />
			Position: Founder<br />
			Discord: koko#0012
			<br /><br />
			<strong>Nathan</strong><br />
			Real Name: Nathan<br />
			Position: Co Founder<br />
			Discord: Nathan#9999
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
';
?>
# HOSTING DEPLOYMENT - CSRF #403 FIX

## Issue
Login gagal di `oro3departemen.my.id` dengan SecurityException #403:
```
CodeIgniter\Security\Exceptions\SecurityException #403
The action you requested is not allowed.
SYSTEMPATH/Security/Security.php at line 263
```

## Root Cause
- **baseURL hardcoded ke localhost:8080** → session/cookie domain mismatch
- **CI_ENVIRONMENT = development** → CSRF settings berbeda antara dev dan production

## Solusi Applied

### 1. Update App Config
- `app/Config/App.php`: baseURL set ke empty string untuk pakai .env value
- `.env`: Set `app.baseURL = 'http://localhost:8080/'` untuk development

### 2. Buat Hosting Config Template
- `.env.hosting`: Template untuk production deployment
- Includes: proper baseURL, CI_ENVIRONMENT=production, HTTPS support, session path notes

### 3. Update .env untuk Dev/Prod Toggle
```
CI_ENVIRONMENT = development  # Local
# CI_ENVIRONMENT = production  # Uncomment untuk production

app.baseURL = 'http://localhost:8080/'
# app.baseURL = 'http://oro3departemen.my.id/'  # Uncomment untuk production
```

## Deployment Steps to Hosting

1. Copy `.env.hosting` ke `.env`:
   ```
   app.baseURL = 'http://oro3departemen.my.id/'
   CI_ENVIRONMENT = production
   ```

2. Ensure writable directories exist and are writable:
   ```
   chmod 755 writable/
   chmod 755 writable/session
   chmod 755 writable/logs
   chmod 755 writable/cache
   ```

3. Test login after deployment

4. If HTTPS enabled, update baseURL:
   ```
   app.baseURL = 'https://oro3departemen.my.id/'
   ```

## Security Config Notes
- CSRF Protection: `session` based (verified in app/Config/Security.php)
- Token Regenerate: `false` (prevents 403 on form resubmit)
- Login view: Has `<?= csrf_field() ?>` ✓

---

# data atau menu finish good

<!-- galon -->

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Finish Goods - Gallon</h4>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width:70px;">No</th>
                    <th>Nama Produk</th>
                    <th>Grade</th>
                    <th>Berat</th>
                    <th>WH ORO</th>
                    <th>WH BAM</th>
                </tr>
            </thead>

            <tbody>

                <tr><td class="text-center">1</td><td>168</td><td>D.0.V-2</td><td class="text-center">750</td><td class="text-end">0</td><td class="text-end">1.700</td></tr>
                <tr><td class="text-center">2</td><td>168</td><td>A.1.V-3</td><td class="text-center">750</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">3</td><td>212</td><td>C.2.B-3</td><td class="text-center">750</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">4</td><td>AL-FALAH</td><td>A+</td><td class="text-center">750</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">5</td><td>AIA</td><td>B.2.B-2</td><td class="text-center">750</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">6</td><td>AGUARIA</td><td>A.1.V-3</td><td class="text-center">750</td><td class="text-end">4</td><td></td></tr>
                <tr><td class="text-center">7</td><td>AQUARAN</td><td>C.2.B-3</td><td class="text-center">750</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">8</td><td>AERZIPS</td><td>A.1.V-3</td><td class="text-center">750</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">9</td><td>AERZIPS</td><td>B.2.B-2</td><td class="text-center">750</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">10</td><td>ALBIL</td><td>A.1.V-4</td><td class="text-center">780</td><td class="text-end">0</td><td></td></tr>

                <!-- lanjutkan data sampai No.138 dengan format yang sama -->

            </tbody>

            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="4" class="text-end">
                        TOTAL
                    </td>
                    <td class="text-end">
                        11.108
                    </td>
                    <td class="text-end">
                        5.340
                    </td>
                </tr>
            </tfoot>

        </table>

    </div>
</div>


<!-- cap galon -->

<div class="card shadow-sm mb-4">

    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Finish Goods - Cap Gallon</h4>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width:70px;">No</th>
                    <th>Nama Produk</th>
                    <th>Warna</th>
                    <th>Berat (Gr)</th>
                    <th>Jumlah (PCS)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>

            <tbody>

                <tr><td class="text-center">1</td><td>AQ 8</td><td>RED 01</td><td class="text-center">9.5</td><td class="text-end">201.000</td><td></td></tr>

                <tr><td class="text-center">2</td><td>ALMA'SOEM</td><td>BLUE 08</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">3</td><td>AIRCOOL</td><td>BLUE 10</td><td class="text-center">9.5</td><td class="text-end">80.000</td><td></td></tr>

                <tr><td class="text-center">4</td><td>BLANK</td><td>WHITE 01</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">5</td><td>BLANK</td><td>BLUE 10</td><td class="text-center">9.5</td><td class="text-end">30.000</td><td></td></tr>

                <tr><td class="text-center">6</td><td>BLANK</td><td>RED 01</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">8</td><td>BLANK (GHAMIR)</td><td>BLUE 04</td><td class="text-center">9.5</td><td class="text-end">15.000</td><td></td></tr>

                <tr><td class="text-center">9</td><td>BLANK (BOLESA) SN</td><td>BLUE 05</td><td class="text-center">9.5</td><td class="text-end">37.500</td><td></td></tr>

                <tr><td class="text-center">10</td><td>BLANK</td><td>BLUE 07</td><td class="text-center">9.5</td><td class="text-end">6.000</td><td></td></tr>

                <tr><td class="text-center">11</td><td>BLANK</td><td>CLEAR</td><td class="text-center">9.5</td><td class="text-end">10.000</td><td></td></tr>

                <tr><td class="text-center">12</td><td>BW</td><td>BLUE 10-1</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">13</td><td>BLU9 SN</td><td>BLUE 07</td><td class="text-center">9</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">14</td><td>BLANK (SAJUAK) SN</td><td>BLUE 07</td><td class="text-center">9</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">15</td><td>LEEQUIL</td><td>BLUE 08</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">16</td><td>G</td><td>BLUE 04</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">17</td><td>JATILUHUR</td><td>BLUE 07</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">18</td><td>MASKUA</td><td>GREEN 03</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">19</td><td>MOUNTOYA NEW</td><td>CLEAR</td><td class="text-center">9.5</td><td class="text-end">150.000</td><td></td></tr>

                <tr><td class="text-center">20</td><td>PREMIER WATER</td><td>BLUE 10</td><td class="text-center">9.5</td><td class="text-end">50.000</td><td></td></tr>

                <tr><td class="text-center">21</td><td>PRISTINE NEW</td><td>GREEN 02</td><td class="text-center">9.5</td><td class="text-end">50.000</td><td></td></tr>

                <tr><td class="text-center">22</td><td>PRISTINE SWT</td><td>GREEN 02</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">23</td><td>PROF</td><td>CLEAR</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">24</td><td>QUELLE</td><td>BLUE 07</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">25</td><td>SWS SN</td><td>WHITE 01</td><td class="text-center">9</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">26</td><td>SIERRA</td><td>BLUE 07</td><td class="text-center">9.5</td><td class="text-end">100.000</td><td class="text-center">15</td></tr>

                <tr><td class="text-center">27</td><td>SUMMIT</td><td>BLUE 04</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">28</td><td>TDS NOL</td><td>WHITE 01</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">29</td><td>T-WATER</td><td>CLEAR</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">30</td><td>UMAXX</td><td>BLUE 04</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">31</td><td>WINRO SN</td><td>RED 01</td><td class="text-center">9</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">32</td><td>VIZ (SN)</td><td>BLUE 04</td><td class="text-center">9</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">33</td><td>BLANK (QFAST)</td><td>BLUE 10</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">34</td><td>PRISTINE LAMA</td><td>GREEN 02</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">35</td><td>WATER STORE SN</td><td>PURPLE 01</td><td class="text-center">9</td><td class="text-end">0</td><td></td></tr>

                <tr><td class="text-center">36</td><td>YORA</td><td>GREEN 03</td><td class="text-center">9.5</td><td class="text-end">0</td><td></td></tr>

            </tbody>

            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTAL</td>
                    <td class="text-end">729.500</td>
                    <td></td>
                </tr>
            </tfoot>

        </table>

    </div>

</div>


<!-- freeform -->

<div class="card shadow-sm mb-4">

    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Finish Goods - Preform</h4>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover table-striped align-middle">

            <thead class="table-dark text-center">
                <tr>
                    <th style="width:70px;">No</th>
                    <th>Nama Produk</th>
                    <th>Warna</th>
                    <th>Berat (Gr)</th>
                    <th>Jumlah (PCS)</th>
                    <th>Packing</th>
                </tr>
            </thead>

            <tbody>

                <tr><td class="text-center">1</td><td>NESTLE CBN 10,12,18,19,21,24,27,30</td><td>BLUE 14</td><td class="text-center">12.5</td><td class="text-end">325.800</td><td class="text-center">25</td></tr>

                <tr><td class="text-center">2</td><td>NESTLE SGN 14,16,18,21,23,25,28,30</td><td>BLUE 14</td><td class="text-center">12.5</td><td class="text-end">1.042.560</td><td class="text-center">80</td></tr>

                <tr><td class="text-center">3</td><td>NESTLE HOLD KARUNG</td><td>BLUE 14</td><td class="text-center">12.5</td><td class="text-end">471.744</td><td class="text-center">312</td></tr>

                <tr><td class="text-center">4</td><td>SORTIR FM NESTLE</td><td>BLUE 14</td><td class="text-center">12.5</td><td class="text-end">55.944</td><td class="text-center">37</td></tr>

                <tr><td class="text-center">5</td><td>FM WK LIV IPS 01</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">0</td><td class="text-center">0</td></tr>

                <tr><td class="text-center">6</td><td>FM WK LIV IPS 02</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">160.272</td><td class="text-center">106</td></tr>

                <tr><td class="text-center">7</td><td>FM MT IPS 01 &amp; 2 HOLD</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">216.216</td><td class="text-center">143</td></tr>

                <tr><td class="text-center">8</td><td>GRAND JADE L IPS 2</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">0</td><td class="text-center">0</td></tr>

                <tr><td class="text-center">9</td><td>TRAIL 15,7 WK LIV IPS 03</td><td>CLR &amp; B29</td><td class="text-center">15.7</td><td class="text-end">138.240</td><td class="text-center">120</td></tr>

                <tr><td class="text-center">10</td><td>FM YS-W01 L.IV IPS 2</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">27.216</td><td class="text-center">18</td></tr>

                <tr><td class="text-center">11</td><td>TRANSISI</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">31.752</td><td class="text-center">21</td></tr>

                <tr><td class="text-center">12</td><td>SIERRA</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">0</td><td class="text-center">0</td></tr>

                <tr><td class="text-center">13</td><td>SIERRA 14,23</td><td>BLUE 20</td><td class="text-center">12.5</td><td class="text-end">0</td><td class="text-center">0</td></tr>

                <tr><td class="text-center">14</td><td>TIRTA JADE</td><td>BLUE 20</td><td class="text-center">12.5</td><td class="text-end">57.456</td><td class="text-center">38</td></tr>

                <tr><td class="text-center">15</td><td>FM JADE IPS 01</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">9.072</td><td class="text-center">6</td></tr>

                <tr><td class="text-center">16</td><td>BLU9 MT IPS 02</td><td>BLUE 14</td><td class="text-center">12.5</td><td class="text-end">786.240</td><td class="text-center">520</td></tr>

                <tr><td class="text-center">17</td><td>ASPEN YS IPS 03</td><td>BLUE 29</td><td class="text-center">12.5</td><td class="text-end">87.552</td><td class="text-center">76</td></tr>

                <tr><td class="text-center">18</td><td>FM JADE L IPS 01</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">1.221.696</td><td class="text-center">808</td></tr>

                <tr><td class="text-center">19</td><td>FM JADE L IPS 02</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">1.295.784</td><td class="text-center">857</td></tr>

                <tr><td class="text-center">20</td><td>ASPEN JADE LIV IPS 03 9,10 &amp; 11</td><td>BLUE 29</td><td class="text-center">15.7</td><td class="text-end">720.000</td><td class="text-center">625</td></tr>

                <tr><td class="text-center">21</td><td>FM YS-W01 IPS 03 10,13,15,17,20,22,24,27,29</td><td>CLEAR</td><td class="text-center">10.9</td><td class="text-end">89.208</td><td class="text-center">59</td></tr>

                <tr><td class="text-center">22</td><td>TRANSISI</td><td>CLEAR</td><td class="text-center">10.9</td><td class="text-end">4.536</td><td class="text-center">3</td></tr>

                <tr><td class="text-center">23</td><td>FM MT IPS 03</td><td>CLEAR</td><td class="text-center">10.9</td><td class="text-end">0</td><td class="text-center">0</td></tr>

                <tr><td class="text-center">24</td><td>FM JADE IPS 03</td><td>CLEAR</td><td class="text-center">10.9</td><td class="text-end">19.656</td><td class="text-center">13</td></tr>

                <tr><td class="text-center">25</td><td>SIERRA RETURN YS IPS 02 BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">492.912</td><td class="text-center">326</td></tr>

                <tr><td class="text-center">26</td><td>FM JADE IPS 02 BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">42.336</td><td class="text-center">28</td></tr>

                <tr><td class="text-center">27</td><td>FM MT IPS 01 BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">0</td><td class="text-center">0</td></tr>

                <tr><td class="text-center">28</td><td>FM JADE LIV IPS 02 BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">3.557.736</td><td class="text-center">2.353</td></tr>

                <tr><td class="text-center">29</td><td>FM WK IPS 02 BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">0</td><td class="text-center">0</td></tr>

                <tr><td class="text-center">30</td><td>FM MT IPS 02 BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">30.240</td><td class="text-center">20</td></tr>

                <tr><td class="text-center">31</td><td>FM WK L.IV IPS 02 BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">0</td><td class="text-center">0</td></tr>

                <tr><td class="text-center">32</td><td>TRANSISI BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">1.309.392</td><td class="text-center">866</td></tr>

                <tr><td class="text-center">33</td><td>FM WK IPS 02 HOLD BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">149.688</td><td class="text-center">99</td></tr>

                <tr><td class="text-center">34</td><td>FM WK L.IV IPS 02 HOLD BAM</td><td>CLEAR</td><td class="text-center">12.5</td><td class="text-end">80.136</td><td class="text-center">53</td></tr>

                <tr><td class="text-center">35</td><td>INDOMARET WK L.IV HOLD BAM</td><td>BLUE 20-1</td><td class="text-center">12.5</td><td class="text-end">225.288</td><td class="text-center">149</td></tr>

                <tr><td class="text-center">36</td><td>FM WK IPS 03 BAM HJU</td><td>CLEAR</td><td class="text-center">10.9</td><td class="text-end">36.288</td><td class="text-center">24</td></tr>

                <tr><td class="text-center">37</td><td>FM JADE LIV IPS 03 BAM</td><td>CLEAR</td><td class="text-center">10.9</td><td class="text-end">455.112</td><td class="text-center">301</td></tr>

                <tr><td class="text-center">43</td><td>FM JADE LIV IPS 03 HOLIS</td><td>CLEAR</td><td class="text-center">10.9</td><td class="text-end">0</td><td class="text-center">0</td></tr>

            </tbody>

            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTAL</td>
                    <td class="text-end">13.140.072</td>
                    <td></td>
                </tr>
            </tfoot>

        </table>

    </div>

</div>


<!-- screw cap -->

<div class="card shadow-sm mb-4">

    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Finish Goods - Screw Cap</h4>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover table-striped align-middle">

            <thead class="table-dark text-center">
                <tr>
                    <th style="width:70px;">No</th>
                    <th>Nama Produk</th>
                    <th>Warna</th>
                    <th>Berat (Gr)</th>
                    <th>Jumlah (PCS)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>

            <tbody>

                <tr><td class="text-center">1</td><td>ALMA'SOEM</td><td>BLUE 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">2</td><td>AMIDIS</td><td>ORANGE 01</td><td class="text-center">1.30</td><td class="text-end">2.000.000</td><td></td></tr>
                <tr><td class="text-center">3</td><td>BLANK</td><td>BLACK 01</td><td class="text-center">1.30</td><td class="text-end">158.000</td><td></td></tr>
                <tr><td class="text-center">4</td><td>BLANK</td><td>BLUE 01-2</td><td class="text-center">1.30</td><td class="text-end">40.000</td><td></td></tr>
                <tr><td class="text-center">5</td><td>BLANK (CA)</td><td>BLUE 01-2</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">6</td><td>BLANK (J WATER)</td><td>GREY 02</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">7</td><td>BLANK</td><td>BLUE 02</td><td class="text-center">1.30</td><td class="text-end">144.000</td><td></td></tr>
                <tr><td class="text-center">8</td><td>BLANK</td><td>BLUE 04</td><td class="text-center">1.30</td><td class="text-end">296.000</td><td></td></tr>
                <tr><td class="text-center">9</td><td>BLANK (Stream)</td><td>BLUE 04</td><td class="text-center">1.30</td><td class="text-end">104.000</td><td class="text-center">28</td></tr>
                <tr><td class="text-center">10</td><td>BLANK</td><td>BLUE 05</td><td class="text-center">1.30</td><td class="text-end">8.000</td><td></td></tr>
                <tr><td class="text-center">11</td><td>BLANK</td><td>CLEAR</td><td class="text-center">1.30</td><td class="text-end">712.000</td><td></td></tr>
                <tr><td class="text-center">12</td><td>BLANK</td><td>PURPLE 01</td><td class="text-center">1.30</td><td class="text-end">64.000</td><td></td></tr>
                <tr><td class="text-center">13</td><td>BLANK</td><td>GOLD 01</td><td class="text-center">1.30</td><td class="text-end">80.000</td><td></td></tr>
                <tr><td class="text-center">14</td><td>BLANK</td><td>GREEN 04</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">15</td><td>BLANK</td><td>GREEN 06</td><td class="text-center">1.30</td><td class="text-end">88.000</td><td></td></tr>
                <tr><td class="text-center">16</td><td>BLANK</td><td>GREEN 02</td><td class="text-center">1.30</td><td class="text-end">40.000</td><td></td></tr>
                <tr><td class="text-center">17</td><td>BLANK</td><td>GREEN 03</td><td class="text-center">1.30</td><td class="text-end">72.000</td><td></td></tr>
                <tr><td class="text-center">18</td><td>BLANK (Lakers)</td><td>GREEN 07</td><td class="text-center">1.30</td><td class="text-end">12.000</td><td></td></tr>
                <tr><td class="text-center">19</td><td>BLANK</td><td>GREEN 08</td><td class="text-center">1.30</td><td class="text-end">64.000</td><td></td></tr>
                <tr><td class="text-center">20</td><td>BLANK</td><td>RED 01</td><td class="text-center">1.30</td><td class="text-end">832.000</td><td></td></tr>
                <tr><td class="text-center">21</td><td>BLANK</td><td>WHITE 01</td><td class="text-center">1.30</td><td class="text-end">120.000</td><td></td></tr>
                <tr><td class="text-center">22</td><td>BLANK</td><td>YELLOW 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">23</td><td>BLANK</td><td>ORANGE 01</td><td class="text-center">1.30</td><td class="text-end">40.000</td><td></td></tr>
                <tr><td class="text-center">24</td><td>BISTO</td><td>ORANGE 01</td><td class="text-center">1.30</td><td class="text-end">472.000</td><td></td></tr>
                <tr><td class="text-center">25</td><td>BLU 9 (DW)</td><td>BLUE 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">26</td><td>BLANK (VICA)</td><td>YELLOW 02</td><td class="text-center">1.30</td><td class="text-end">0</td><td class="text-center">21</td></tr>
                <tr><td class="text-center">27</td><td>BLANK (Guardian)</td><td>ORANGE 02</td><td class="text-center">1.30</td><td class="text-end">0</td><td class="text-center">23</td></tr>
                <tr><td class="text-center">28</td><td>NESTLE</td><td>BLUE 01</td><td class="text-center">1.30</td><td class="text-end">2.720.000</td><td>21 &amp; 10,24</td></tr>
                <tr><td class="text-center">29</td><td>BLANK (Watsons)</td><td>BLUE 13</td><td class="text-center">1.30</td><td class="text-end">0</td><td>10</td></tr>
                <tr><td class="text-center">30</td><td>BLANK (Superindo)</td><td>RED 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">31</td><td>ES ER CE</td><td>RED 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">32</td><td>FATQUA</td><td>BLUE 04</td><td class="text-center">1.30</td><td class="text-end">160.000</td><td></td></tr>
                <tr><td class="text-center">33</td><td>SIERRA</td><td>BLUE 03</td><td class="text-center">1.30</td><td class="text-end">0</td><td>15,21,27</td></tr>
                <tr><td class="text-center">34</td><td>OMBE (DW)</td><td>BLUE 17</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">35</td><td>DELQUA</td><td>GREEN 11</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">36</td><td>DELQUA</td><td>GREEN 02</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">37</td><td>TIRTA</td><td>BLUE 01-2</td><td class="text-center">1.30</td><td class="text-end">16.000</td><td></td></tr>
                <tr><td class="text-center">38</td><td>SHOFA</td><td>GREEN 06</td><td class="text-center">1.30</td><td class="text-end">152.000</td><td></td></tr>
                <tr><td class="text-center">39</td><td>PRIMA PANDAAN</td><td>BLUE 21</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">40</td><td>BLANK</td><td>PINK 02</td><td class="text-center">1.30</td><td class="text-end">224.000</td><td></td></tr>
                <tr><td class="text-center">41</td><td>BLANK</td><td>BLUE 01</td><td class="text-center">1.30</td><td class="text-end">1.088.000</td><td></td></tr>
                <tr><td class="text-center">42</td><td>BLANK (Sanqua)</td><td>BLUE 23</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">43</td><td>AIR ALAM</td><td>BLUE 22</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">44</td><td>SISESA</td><td>PURPLE 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">45</td><td>PRIMA SENTUL</td><td>BLUE 21</td><td class="text-center">1.30</td><td class="text-end">8.000</td><td></td></tr>
                <tr><td class="text-center">46</td><td>TRIPANCA</td><td>BLUE 04</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">47</td><td>BLANK (HN)</td><td>BLUE 24</td><td class="text-center">1.30</td><td class="text-end">152.000</td><td></td></tr>
                <tr><td class="text-center">48</td><td>BLANK (BI)</td><td>GREEN 09</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">49</td><td>INDOMARET</td><td>BLUE 01</td><td class="text-center">1.30</td><td class="text-end">504.000</td><td></td></tr>
                <tr><td class="text-center">50</td><td>LEVIOS</td><td>BLUE 01</td><td class="text-center">1.30</td><td class="text-end">8.000</td><td></td></tr>
                <tr><td class="text-center">51</td><td>BLANK (SIERRA)</td><td>BLACK 01</td><td class="text-center">1.30</td><td class="text-end">220.000</td><td></td></tr>
                <tr><td class="text-center">52</td><td>BLANK (SIERRA)</td><td>RED 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td>15,20,25</td></tr>
                <tr><td class="text-center">53</td><td>LEVIOS</td><td>BLUE 17</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">54</td><td>BLANK (ASMI)</td><td>BROWN 01</td><td class="text-center">1.30</td><td class="text-end">104.000</td><td></td></tr>
                <tr><td class="text-center">55</td><td>BLANK</td><td>GREEN 01</td><td class="text-center">1.30</td><td class="text-end">152.000</td><td></td></tr>
                <tr><td class="text-center">56</td><td>BLANK (CLUB)</td><td>BLUE 01-1</td><td class="text-center">1.30</td><td class="text-end">8.000</td><td></td></tr>
                <tr><td class="text-center">57</td><td>BLANK (VIONA)</td><td>GREEN 02</td><td class="text-center">1.30</td><td class="text-end">104.000</td><td></td></tr>
                <tr><td class="text-center">58</td><td>ALKAKUA</td><td>BLACK 01</td><td class="text-center">1.30</td><td class="text-end">504.000</td><td></td></tr>
                <tr><td class="text-center">59</td><td>BLANK (CLUB)</td><td>BLUE 25</td><td class="text-center">1.30</td><td class="text-end">8.000</td><td></td></tr>
                <tr><td class="text-center">60</td><td>HEVITRO</td><td>GREEN 10</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">61</td><td>BLANK</td><td>BLUE 03</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">62</td><td>BLANK</td><td>BLUE 13</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">63</td><td>BLANK (BOX POLOS)</td><td>GREEN 03</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">64</td><td>BLANK (BOX POLOS)</td><td>BLACK 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">65</td><td>BLANK (BOX POLOS)</td><td>WHITE 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">66</td><td>BLANK (VIONA)</td><td>BLUE 02</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">67</td><td>BLANK (AMOZ)</td><td>SILVER 01</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">68</td><td>BLANK (LAVIDA)</td><td>BLUE 08</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">69</td><td>BLANK (POLOS)</td><td>BLUE 02</td><td class="text-center">1.30</td><td class="text-end">352.000</td><td></td></tr>
                <tr><td class="text-center">70</td><td>BLANK (PELANGI)</td><td>PINK 02</td><td class="text-center">1.30</td><td class="text-end">0</td><td></td></tr>
                <tr><td class="text-center">71</td><td>BLANK (ASPEN)</td><td>BLUE 30</td><td class="text-center">1.45</td><td class="text-end">12.864.000</td><td></td></tr>

            </tbody>

            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTAL</td>
                    <td class="text-end">24.694.000</td>
                    <td></td>
                </tr>
            </tfoot>

        </table>

    </div>

</div>
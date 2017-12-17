<?php

pdo_query("DROP TABLE IF EXISTS ".tablename('dxf_ycj_cate').";");
pdo_query("DROP TABLE IF EXISTS ".tablename('dxf_ycj_category').";");
pdo_query("DROP TABLE IF EXISTS ".tablename('dxf_ycj_daili').";");
pdo_query("DROP TABLE IF EXISTS ".tablename('dxf_ycj_job').";");
pdo_query("DROP TABLE IF EXISTS ".tablename('dxf_ycj_jobuser').";");
pdo_query("DROP TABLE IF EXISTS ".tablename('dxf_ycj_slide').";");
pdo_query("DROP TABLE IF EXISTS ".tablename('dxf_ycj_user').";");
pdo_query("DROP TABLE IF EXISTS ".tablename('dxf_ycj_activity').";");
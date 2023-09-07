


insert into public.cities
select * from cities;

select * 
from "cities" 
where ("status" = '1'
	or "province" ilike '%wan%'
	or "city" ilike '%wan%'
	or "district" ilike '%wan%'
	or "village" ilike '%wan%'
	) 
	and "cities"."deleted_at" is null
;


select * 
from "countries" 
where "status" = '1'
	 and ("name" ilike '%wan%'
			or "region_name" ilike '%wan%'
			or "region_name" ilike '%wan%'
			or "intermediate_region_name" ilike '%wan%'
			or "capital_city" ilike '%wan%'
			) 
		and "countries"."deleted_at" is null;
	
insert into users (
	uuid, 
	first_name, 
	last_name, 
	email, 
	validation_code, 
	status, 
	updated_at, 
	created_at
	) 
values (
	'2ecfa0a5-a14e-445f-9342-d293fea50f27', 
	'Luqito Masdar', 
	'Hilal', 
	'hilal.lucky@gmail.com', 
	'fqRuQXGtJc1X0E2KnyMozu9f0ytlJdhCnsrn51IZHzPKN7O7d7N2W', 
	'1', 
	'2023-09-05 10:11:56', 
	'2023-09-05 10:11:56'
	);
	


WITH RECURSIVE Genealogy AS (
  -- Base case: Find the root member(s)
  SELECT member_id, sponsor_id, position, 0 AS level
  FROM mlm_network
  WHERE sponsor_id IS NULL -- Assuming root members have a NULL sponsor_id
  
  UNION ALL
  
  -- Recursive case: Join with the previous level
  SELECT n.member_id, n.sponsor_id, n.position, g.level + 1
  FROM mlm_network AS n
  INNER JOIN Genealogy AS g ON n.sponsor_id = g.member_id
)
SELECT * FROM Genealogy
ORDER BY level, position;




-- MENAMPILKAN JARINGAN MULAI DARI SPONSOR sampai dengan downline terbawah
WITH RECURSIVE Genealogy AS (
  -- Base case: Find the root member(s)
  SELECT id, first_name, last_name, sponsor_id, sponsor_uuid, 0 AS level
  FROM members
  WHERE id = 29 -- Assuming root members have a NULL sponsor_id
  
  UNION ALL
  
  -- Recursive case: Join with the previous level
  SELECT n.id, n.first_name, n.last_name, n.sponsor_id, n.sponsor_uuid, g.level + 1
  FROM members n
  INNER JOIN Genealogy AS g ON n.sponsor_id = g.id
)
SELECT * FROM Genealogy
ORDER BY level;


-- MENAMPILKAN JARINGAN MULAI DARI UPLINE sampai dengan downline terbawah
WITH RECURSIVE Genealogy AS (
  -- Base case: Find the root member(s)
  SELECT id, first_name, last_name, placement_id, placement_uuid, 0 AS level
  FROM members
  WHERE id = 43 -- Assuming root members have a NULL sponsor_id
  
  UNION ALL
  
  -- Recursive case: Join with the previous level
  SELECT n.id, n.first_name, n.last_name, n.placement_id, n.placement_uuid, g.level + 1
  FROM members n
  INNER JOIN Genealogy AS g ON n.placement_id = g.id
)
SELECT * FROM Genealogy
ORDER BY level;


  -- Recursive case: Join with the previous level
  SELECT n.id, n.first_name, n.last_name, n.placement_id, n.placement_uuid
  FROM members n
  where n.placement_id = 43;
  

select m.id, uuid , sponsor_id, sponsor_uuid, placement_id, placement_uuid 
from members m
where id>42;

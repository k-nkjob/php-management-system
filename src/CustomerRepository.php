<?php
declare(strict_types=1);
final class CustomerRepository{
 public function __construct(private PDO $pdo){}
 public function paginate(string $q,int $page,int $per=10):array{$q=trim($q);$where='';$p=[];if($q!==''){$where=' WHERE name LIKE :query OR email LIKE :query OR company LIKE :query';$p['query']='%'.$q.'%';}$s=$this->pdo->prepare('SELECT COUNT(*) FROM customers'.$where);$s->execute($p);$total=(int)$s->fetchColumn();$pages=max(1,(int)ceil($total/$per));$page=min(max(1,$page),$pages);$s=$this->pdo->prepare('SELECT id,name,email,phone,company,created_at FROM customers'.$where.' ORDER BY id DESC LIMIT :limit OFFSET :offset');foreach($p as $k=>$v)$s->bindValue(':'.$k,$v);$s->bindValue(':limit',$per,PDO::PARAM_INT);$s->bindValue(':offset',($page-1)*$per,PDO::PARAM_INT);$s->execute();return ['items'=>$s->fetchAll(),'total'=>$total,'page'=>$page,'pages'=>$pages];}
 public function find(int $id):?array{$s=$this->pdo->prepare('SELECT * FROM customers WHERE id=:id');$s->execute(['id'=>$id]);return $s->fetch()?:null;}
 public function create(array $d):void{$s=$this->pdo->prepare('INSERT INTO customers(name,email,phone,company,created_at,updated_at) VALUES(:name,:email,:phone,:company,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)');$s->execute($this->fields($d));}
 public function update(int $id,array $d):void{$f=$this->fields($d);$f['id']=$id;$s=$this->pdo->prepare('UPDATE customers SET name=:name,email=:email,phone=:phone,company=:company,updated_at=CURRENT_TIMESTAMP WHERE id=:id');$s->execute($f);}
 public function delete(int $id):bool{$s=$this->pdo->prepare('DELETE FROM customers WHERE id=:id');$s->execute(['id'=>$id]);return $s->rowCount()>0;}
 private function fields(array $d):array{return ['name'=>trim((string)$d['name']),'email'=>mb_strtolower(trim((string)$d['email'])),'phone'=>trim((string)($d['phone']??'')),'company'=>trim((string)($d['company']??''))];}
}

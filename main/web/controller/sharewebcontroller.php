<?php
class ShareWebController extends Controller {
    public $components = array('Resource');
    
    public function www_show() {
        $id = $this->request->query['id'];
        if ( false == empty($id) && true == is_numeric($id) ) {
            //查找出来话题 和 作者名称
            $talk = Share::first(array('conditions' => 'share.id = ? and share.type = '.Share::TYPE_TALK,
                                       'fields' => array('u.nickname', 'u.avatar', 'u2.nickname as nickname2', 'u2.avatar as avatar2', 't.voice', 't.voice_time', 't.themeid', 't.roomid', 't.id'),
                                       'joins' => array(array('type' => 'left','alias' => 't','table' => 'qyh_room.talks', 'conditions' => "share.objectid = t.id" ),
                                                        array('type' => 'left','alias' => 'u','table' => 'user_profiles', 'conditions' => "u.accountid = share.accountid" ),
                                                        array('type' => 'left','alias' => 'u2','table' => 'user_profiles', 'conditions' => "u2.accountid = share.ownerid" ))
                                      ),
                                  array($id));
            if ( true == $talk ) {
                //语音转换 找到对应的MP3
                $talk->voice = array('caf' => $talk->voice,
                                     'mp3' => $this->Resource->voice_mp3_version($talk->voice));
                //找到标签和房间名称
                $tag = RoomTag::find(array('conditions' => 'roomtag.roomid = ? ',
                                          'fields' => array('t.name', 'r.title'),
                                          'joins' => array(array('type' => 'left','alias' => 'r','table' => 'rooms', 'conditions' => "roomtag.roomid = r.id" ),
                                                           array('type' => 'left','alias' => 't','table' => 'tags', 'conditions' => "roomtag.tagid = t.id" ))
                                      ),
                                      array($talk->roomid));
                //计算总数
                $comment_num = Comment::first(array('conditions' => 'talkid = ? ',
                                                    'fields' => array('count(*) as num')),
                                              array($talk->id));
                $title = '';
                foreach ( $tag as $_tag ) {
                    $title .= '['.$_tag->name.']';
                }
                //找话题的评论
                $comment = Comment::find(array('conditions' => 'comment.talkid = ?',
                                               'fields' => array('comment.id', 'comment.voice', 'comment.voice_time', 'comment.voice_image', 'u.avatar', 'u.nickname', 'comment.voice_fid'),
                                               'joins' => array(array('type' => 'left','alias' => 'u','table' => 'qyh_user.user_profiles', 'conditions' => "comment.accountid = u.accountid" )),
                                               'order' => 'comment.id desc',
                                               'limit' => '5'
                                               ),
                                         array($talk->id));
                foreach ( $comment as & $_comment ) {
                    $_comment->voice_fid = $comment_num->num;
                    $comment_num->num --;
                    $_comment->voice = array('caf' => $_comment->voice,
                                             'mp3' => $this->Resource->voice_mp3_version($_comment->voice));
                }
                $this->set('url', 'http://t.htapp.cn/img/weibo');
                $this->set('title', $title.$_tag->title);
                $this->set('talkid', $talk->id);
                $this->set('avatar', $talk->avatar);
                $this->set('nickname', $talk->nickname);
                $this->set('avatar2', $talk->avatar2);
                $this->set('nickname2', $talk->nickname2);
                $this->set('voice', $talk->voice);
                $this->set('voice_time', $talk->voice_time);
                $this->set('bg_image', $this->Resource->getRoomBackgroundImageURL($talk->themeid, false)); 
                $this->set('comment', $comment);

                $this->view = 'share';
            }
        }
    }
    
}